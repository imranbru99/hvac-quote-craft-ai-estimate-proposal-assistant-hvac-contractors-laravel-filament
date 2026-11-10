<?php

namespace App\Services;

use App\Models\AuditEvent;
use App\Models\Estimate;
use App\Models\Proposal;
use App\Models\ProposalFollowup;
use App\Models\ServiceRequest;
use Illuminate\Support\Str;

class AiQuoteService
{
    /**
     * MVP: intake service request → AI draft estimate → human approval → audit log.
     */
    public function draftEstimateFromIntake(ServiceRequest $request): Estimate
    {
        $equipment = $request->equipment ?: 'Residential HVAC system';
        $lineItems = $this->suggestLineItems($equipment, $request->notes);

        $subtotal = collect($lineItems)->sum(fn ($i) => (float) $i['qty'] * (float) $i['unit_price']);
        $tax = round($subtotal * 0.0825, 2);
        $total = $subtotal + $tax;

        $draft = sprintf(
            "AI DRAFT ESTIMATE\nCustomer intake: %s\nEquipment: %s\nAddress: %s\n\nLine items:\n%s\nSubtotal: \$%.2f\nTax (8.25%%): \$%.2f\nTotal: \$%.2f\n\nSuggested validity: 30 days. Human approval required before customer send.",
            $request->name ?? $request->code,
            $equipment,
            $request->address ?? 'TBD',
            collect($lineItems)->map(fn ($i) => sprintf('- %s x%s @ $%.2f = $%.2f', $i['description'], $i['qty'], $i['unit_price'], $i['qty'] * $i['unit_price']))->implode("\n"),
            $subtotal,
            $tax,
            $total
        );

        $estimate = Estimate::query()->create([
            'name' => 'HVAC Estimate — '.($request->name ?? $request->code),
            'code' => 'EST-'.strtoupper(Str::random(6)),
            'customer_id' => $request->customer_id,
            'service_request_id' => $request->id,
            'status' => 'draft',
            'equipment' => $equipment,
            'address' => $request->address,
            'line_items' => $lineItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => 0,
            'total' => $total,
            'valid_days' => 30,
            'issued_at' => now(),
            'valid_until' => now()->addDays(30),
            'ai_draft' => $draft,
            'notes' => 'Generated from service request '.$request->code,
            'messaging_consent' => false,
            'is_active' => true,
            'meta' => ['source' => 'ai_quote_service', 'workflow' => 'intake→draft→approve→audit'],
        ]);

        $this->audit('estimate', $estimate->id, 'ai_draft_created', 'system', 'AI drafted estimate from intake '.$request->code);

        return $estimate;
    }

    public function approveEstimate(Estimate $estimate, string $actor = 'owner'): Estimate
    {
        $estimate->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $actor,
        ]);

        $this->audit('estimate', $estimate->id, 'approved', $actor, 'Human approved draft estimate '.$estimate->code);

        return $estimate->fresh();
    }

    public function buildProposal(Estimate $estimate, string $actor = 'system'): Proposal
    {
        $title = 'HVAC Proposal — '.($estimate->name ?? $estimate->code);
        $summary = "Prepared for your property at ".($estimate->address ?: 'your location').". Total investment: \$".number_format((float) $estimate->total, 2).".";
        $scope = collect($estimate->line_items ?? [])
            ->map(fn ($i) => sprintf('• %s (qty %s)', $i['description'], $i['qty']))
            ->implode("\n");
        $terms = "Estimate valid for {$estimate->valid_days} days. Work scheduled after signed acceptance. Licensing and safety compliance included. Payment terms: 50% deposit, balance on completion.";

        $proposal = Proposal::query()->create([
            'name' => $title,
            'code' => 'PRO-'.strtoupper(Str::random(6)),
            'estimate_id' => $estimate->id,
            'customer_id' => $estimate->customer_id,
            'status' => 'draft',
            'title' => $title,
            'summary' => $summary,
            'scope_of_work' => $scope ?: $estimate->ai_draft,
            'terms' => $terms,
            'total' => $estimate->total,
            'is_active' => true,
            'meta' => ['ai' => true, 'actor' => $actor],
        ]);

        $this->audit('proposal', $proposal->id, 'ai_draft_created', $actor, 'AI built proposal from estimate '.$estimate->code);

        return $proposal;
    }

    public function approveAndSendProposal(Proposal $proposal, string $actor = 'owner'): Proposal
    {
        $proposal->update([
            'status' => 'sent',
            'approved_at' => now(),
            'sent_at' => now(),
            'approved_by' => $actor,
        ]);

        if ($proposal->estimate) {
            $proposal->estimate->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        $this->audit('proposal', $proposal->id, 'approved_and_sent', $actor, 'Approved and sent proposal '.$proposal->code);

        return $proposal->fresh();
    }

    public function draftFollowup(Proposal $proposal): ProposalFollowup
    {
        $body = sprintf(
            "Hi — following up on proposal %s for \$%.2f. Happy to adjust options or schedule a site walk. Reply STOP to opt out. — HvacQuoteCraft",
            $proposal->code,
            (float) $proposal->total
        );

        $followup = ProposalFollowup::query()->create([
            'name' => 'Follow-up for '.$proposal->code,
            'code' => 'FU-'.strtoupper(Str::random(6)),
            'proposal_id' => $proposal->id,
            'estimate_id' => $proposal->estimate_id,
            'customer_id' => $proposal->customer_id,
            'channel' => 'email',
            'status' => 'draft',
            'body' => $body,
            'scheduled_for' => now()->addDays(2),
            'consent_ok' => (bool) ($proposal->estimate?->messaging_consent),
            'is_active' => true,
            'meta' => ['ai' => true],
        ]);

        $this->audit('proposal_followup', $followup->id, 'ai_draft_created', 'system', 'AI drafted follow-up for '.$proposal->code);

        return $followup;
    }

    public function approveAndSendFollowup(ProposalFollowup $followup, string $actor = 'owner'): ProposalFollowup
    {
        if (! $followup->consent_ok) {
            throw new \RuntimeException('Messaging consent required before sending follow-ups.');
        }

        $followup->update([
            'status' => 'sent',
            'approved_at' => now(),
            'sent_at' => now(),
            'approved_by' => $actor,
        ]);

        $this->audit('proposal_followup', $followup->id, 'approved_and_sent', $actor, 'Approved follow-up '.$followup->code);

        return $followup->fresh();
    }

    protected function suggestLineItems(string $equipment, ?string $notes): array
    {
        $items = [
            ['sku' => 'LAB-EST', 'description' => 'On-site diagnostic & load calculation', 'qty' => 1, 'unit_price' => 185.00],
            ['sku' => 'EQ-MAIN', 'description' => 'Primary HVAC equipment ('.$equipment.')', 'qty' => 1, 'unit_price' => 4200.00],
            ['sku' => 'LAB-INST', 'description' => 'Installation labor & commissioning', 'qty' => 1, 'unit_price' => 1650.00],
            ['sku' => 'MAT-MISC', 'description' => 'Materials, refrigerant, pads & fittings', 'qty' => 1, 'unit_price' => 480.00],
        ];

        if ($notes && str_contains(strtolower($notes), 'heat pump')) {
            $items[1]['description'] = 'High-efficiency heat pump package';
            $items[1]['unit_price'] = 5850.00;
        }

        if ($notes && str_contains(strtolower($notes), 'duct')) {
            $items[] = ['sku' => 'DUCT-MOD', 'description' => 'Duct modification & sealing', 'qty' => 1, 'unit_price' => 950.00];
        }

        return $items;
    }

    protected function audit(string $entityType, int $entityId, string $action, string $actor, string $notes): void
    {
        AuditEvent::query()->create([
            'name' => strtoupper($action),
            'code' => 'AUD-'.strtoupper(Str::random(6)),
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'actor' => $actor,
            'notes' => $notes,
            'is_active' => true,
        ]);
    }
}

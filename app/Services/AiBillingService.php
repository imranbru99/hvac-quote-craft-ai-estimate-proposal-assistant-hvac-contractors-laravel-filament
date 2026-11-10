<?php

namespace App\Services;

use App\Models\AuditEvent;
use App\Models\Invoice;
use App\Models\JobTicket;
use App\Models\PaymentReminder;
use Illuminate\Support\Str;

class AiBillingService
{
    /**
     * MVP: intake job ticket → AI draft invoice → human approval → audit log.
     */
    public function draftInvoiceFromJob(JobTicket $ticket): Invoice
    {
        $subtotal = (float) $ticket->parts_total + (float) $ticket->labor_total;
        $tax = round($subtotal * 0.08, 2);
        $total = $subtotal + $tax;

        $draft = sprintf(
            "AI DRAFT INVOICE\nCustomer job: %s\nLabor: %.2f hrs @ \$%.2f\nParts: \$%.2f\nSubtotal: \$%.2f\nTax (8%%): \$%.2f\nTotal: \$%.2f\n\nSuggested terms: Net 15. Reminder consent required before send.",
            $ticket->name ?? $ticket->code,
            (float) $ticket->labor_hours,
            (float) $ticket->labor_total,
            (float) $ticket->parts_total,
            $subtotal,
            $tax,
            $total
        );

        $invoice = Invoice::query()->create([
            'name' => 'HVAC Invoice — '.($ticket->name ?? $ticket->code),
            'code' => 'INV-'.strtoupper(Str::random(6)),
            'customer_id' => $ticket->customer_id,
            'job_ticket_id' => $ticket->id,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'amount_paid' => 0,
            'amount_due' => $total,
            'status' => 'draft',
            'payment_status' => 'unpaid',
            'issued_at' => now(),
            'due_at' => now()->addDays(15),
            'ai_draft' => $draft,
            'notes' => 'Generated from job ticket '.$ticket->code,
            'reminder_consent' => false,
            'is_active' => true,
            'meta' => ['source' => 'ai_billing_service', 'workflow' => 'intake→draft→approve→audit'],
        ]);

        $this->audit('invoice', $invoice->id, 'ai_draft_created', 'system', 'AI drafted invoice from job ticket '.$ticket->code);

        return $invoice;
    }

    public function approveInvoice(Invoice $invoice, string $actor = 'owner'): Invoice
    {
        $invoice->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $actor,
        ]);

        $this->audit('invoice', $invoice->id, 'approved', $actor, 'Human approved draft invoice '.$invoice->code);

        return $invoice->fresh();
    }

    public function reconcilePayment(Invoice $invoice, float $amount): Invoice
    {
        $paid = (float) $invoice->amount_paid + $amount;
        $due = max(0, (float) $invoice->total - $paid);
        $paymentStatus = $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');

        $invoice->update([
            'amount_paid' => $paid,
            'amount_due' => $due,
            'payment_status' => $paymentStatus,
            'status' => $due <= 0 ? 'paid' : $invoice->status,
        ]);

        $this->audit('invoice', $invoice->id, 'payment_reconciled', 'system', "Recorded payment {$amount}; due {$due}");

        return $invoice->fresh();
    }

    public function draftReminder(Invoice $invoice): PaymentReminder
    {
        $body = sprintf(
            "Hi — friendly reminder that invoice %s for \$%.2f is due on %s. Reply STOP to opt out. — HvacQuoteCraft",
            $invoice->code,
            (float) $invoice->amount_due,
            optional($invoice->due_at)->toDateString() ?? 'soon'
        );

        $reminder = PaymentReminder::query()->create([
            'name' => 'Reminder for '.$invoice->code,
            'code' => 'REM-'.strtoupper(Str::random(6)),
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'channel' => 'email',
            'status' => 'draft',
            'body' => $body,
            'scheduled_for' => now()->addDay(),
            'consent_ok' => (bool) $invoice->reminder_consent,
            'is_active' => true,
            'meta' => ['ai' => true],
        ]);

        $this->audit('payment_reminder', $reminder->id, 'ai_draft_created', 'system', 'AI drafted payment reminder for '.$invoice->code);

        return $reminder;
    }

    public function approveAndSendReminder(PaymentReminder $reminder, string $actor = 'owner'): PaymentReminder
    {
        if (! $reminder->consent_ok) {
            throw new \RuntimeException('Messaging consent required before sending reminders.');
        }

        $reminder->update([
            'status' => 'sent',
            'approved_at' => now(),
            'sent_at' => now(),
            'approved_by' => $actor,
        ]);

        $this->audit('payment_reminder', $reminder->id, 'approved_and_sent', $actor, 'Approved reminder '.$reminder->code);

        return $reminder->fresh();
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

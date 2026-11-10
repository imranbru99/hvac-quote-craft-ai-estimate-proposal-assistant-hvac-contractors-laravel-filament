<?php

namespace App\Services;

use App\Models\AuditEvent;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\JobTicket;
use App\Models\Proposal;
use App\Models\ProposalFollowup;
use App\Models\ServiceRequest;
use App\Models\Technician;

class BusinessMetrics
{
    public function overview(): array
    {
        return [
            'customers' => Customer::query()->count(),
            'service_requests' => ServiceRequest::query()->count(),
            'job_tickets' => JobTicket::query()->count(),
            'technicians' => Technician::query()->count(),
            'estimates' => Estimate::query()->count(),
            'draft_estimates' => Estimate::query()->where('status', 'draft')->count(),
            'pipeline_value' => (float) Estimate::query()->whereIn('status', ['draft', 'approved', 'sent'])->sum('total'),
            'proposals' => Proposal::query()->count(),
            'followups' => ProposalFollowup::query()->count(),
            'audit_events' => AuditEvent::query()->count(),
            'application' => config('app.name'),
            'author' => config('app.author'),
        ];
    }
}

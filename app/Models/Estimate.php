<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Estimate extends Model
{
    protected $fillable = [
        'name', 'code', 'customer_id', 'branch_id', 'service_request_id', 'job_ticket_id',
        'technician_id', 'status', 'equipment', 'address', 'line_items', 'subtotal', 'tax',
        'discount', 'total', 'valid_days', 'issued_at', 'valid_until', 'approved_at',
        'approved_by', 'sent_at', 'accepted_at', 'ai_draft', 'notes', 'messaging_consent',
        'is_active', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'messaging_consent' => 'boolean',
            'line_items' => 'array',
            'meta' => 'array',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'issued_at' => 'datetime',
            'valid_until' => 'datetime',
            'approved_at' => 'datetime',
            'sent_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function jobTicket(): BelongsTo
    {
        return $this->belongsTo(JobTicket::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function proposal(): HasOne
    {
        return $this->hasOne(Proposal::class);
    }

    public function followups(): HasMany
    {
        return $this->hasMany(ProposalFollowup::class);
    }
}

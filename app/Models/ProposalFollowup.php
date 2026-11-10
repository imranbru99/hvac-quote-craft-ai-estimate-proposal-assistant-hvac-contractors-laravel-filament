<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalFollowup extends Model
{
    protected $fillable = [
        'name', 'code', 'proposal_id', 'estimate_id', 'customer_id', 'channel', 'status',
        'body', 'scheduled_for', 'approved_at', 'sent_at', 'approved_by', 'consent_ok',
        'is_active', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'consent_ok' => 'boolean',
            'meta' => 'array',
            'scheduled_for' => 'datetime',
            'approved_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}

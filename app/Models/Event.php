<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasUlids;

    // Events are immutable. We keep Laravel's timestamp management on so
    // created_at is populated on insert and reloaded onto the model, but
    // we disable updated_at entirely (the column doesn't exist).
    public $timestamps = true;

    const UPDATED_AT = null;

    protected $fillable = [
        'workspace_id',
        'type',
        'payload',
        'idempotency_key',
        'source_ip',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Per-event delivery summary. Until Phase 3 ships fan-out + delivery
     * tracking, every counter is 0.
     */
    public function deliveriesSummary(): array
    {
        return [
            'total' => 0,
            'succeeded' => 0,
            'failed' => 0,
            'pending' => 0,
        ];
    }
}

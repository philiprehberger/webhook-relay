<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Per-event delivery summary aggregated from the deliveries table.
     */
    public function deliveriesSummary(): array
    {
        $counts = $this->deliveries()
            ->selectRaw('status, count(*) as n')
            ->groupBy('status')
            ->pluck('n', 'status');

        return [
            'total' => (int) $counts->sum(),
            'succeeded' => (int) ($counts[Delivery::STATUS_SUCCESS] ?? 0),
            'failed' => (int) (($counts[Delivery::STATUS_FAILED] ?? 0) + ($counts[Delivery::STATUS_DEAD] ?? 0)),
            'pending' => (int) ($counts[Delivery::STATUS_PENDING] ?? 0),
        ];
    }
}

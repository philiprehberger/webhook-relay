<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Subscription extends Model
{
    use HasUlids;

    public const STATE_ACTIVE = 'active';
    public const STATE_PAUSED = 'paused';
    public const STATE_DISABLED = 'disabled';

    public const SECRET_ROTATION_GRACE_HOURS = 48;

    protected $fillable = [
        'workspace_id',
        'name',
        'url',
        'signing_secret',
        'previous_signing_secret',
        'secret_rotated_at',
        'event_filter',
        'state',
        'consecutive_failures',
        'paused_at',
    ];

    protected $hidden = ['signing_secret', 'previous_signing_secret'];

    protected function casts(): array
    {
        return [
            'consecutive_failures' => 'integer',
            'paused_at' => 'datetime',
            'secret_rotated_at' => 'datetime',
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

    public function isActive(): bool
    {
        return $this->state === self::STATE_ACTIVE;
    }

    public function pause(): void
    {
        $this->update([
            'state' => self::STATE_PAUSED,
            'paused_at' => now(),
        ]);
    }

    public function resume(): void
    {
        $this->update([
            'state' => self::STATE_ACTIVE,
            'paused_at' => null,
            'consecutive_failures' => 0,
        ]);
    }

    /**
     * Generate a new signing secret. The old one stays valid as
     * `previous_signing_secret` for SECRET_ROTATION_GRACE_HOURS so receivers
     * can swap their verifier without dropping requests.
     */
    public function rotateSecret(): string
    {
        $newSecret = self::generateSecret();

        $this->update([
            'previous_signing_secret' => $this->signing_secret,
            'signing_secret' => $newSecret,
            'secret_rotated_at' => now(),
        ]);

        return $newSecret;
    }

    public static function generateSecret(): string
    {
        return 'whsec_'.Str::random(48);
    }

    /**
     * Match an event type against this subscription's filter.
     *
     * Filter forms:
     *   "*"               -> match everything
     *   "order.created"   -> exact match
     *   "order.*"         -> prefix-glob (matches order.X, order.Y.Z, etc.)
     */
    public function matches(string $eventType): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $filter = $this->event_filter;

        if ($filter === '*') {
            return true;
        }

        if (! str_contains($filter, '*')) {
            return $filter === $eventType;
        }

        $pattern = '/^'.str_replace(['.', '*'], ['\\.', '.*'], $filter).'$/';

        return (bool) preg_match($pattern, $eventType);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Plan D-8 / SPEC §6.1 — scheme-usage evidence in the database, not logs.
 *
 * Retiring `webhook-relay-v0` requires 30 consecutive days of no traffic on it.
 * Log retention on this host is 30 days with a 200 MB cap — exactly the window
 * the decision needs, and therefore far too thin to be the evidence for it.
 */
class SignatureSchemeUsage extends Model
{
    protected $table = 'signature_scheme_usage';

    protected $fillable = ['subscription_id', 'scheme', 'requests', 'first_seen_at', 'last_seen_at'];

    protected function casts(): array
    {
        return ['requests' => 'integer', 'first_seen_at' => 'datetime', 'last_seen_at' => 'datetime'];
    }

    public static function record(string $subscriptionId, string $scheme): void
    {
        $now = now();

        DB::table('signature_scheme_usage')->upsert(
            [[
                'subscription_id' => $subscriptionId,
                'scheme' => $scheme,
                'requests' => 1,
                'first_seen_at' => $now,
                'last_seen_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]],
            ['subscription_id', 'scheme'],
            ['requests' => DB::raw('requests + 1'), 'last_seen_at' => $now, 'updated_at' => $now],
        );
    }

    public static function usedWithinDays(string $scheme, int $days): bool
    {
        return static::query()
            ->where('scheme', $scheme)
            ->where('last_seen_at', '>=', now()->subDays($days))
            ->exists();
    }
}

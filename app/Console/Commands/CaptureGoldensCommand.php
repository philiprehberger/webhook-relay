<?php

namespace App\Console\Commands;

use App\Services\WebhookSigner;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

/**
 * Plan 0.5.1 / D-3 — deterministic delivery goldens.
 *
 * Captured BEFORE the Interchange adoption so the regression oracle cannot be
 * retrofitted to whatever the new code produces. Seeded secret, frozen clock,
 * fixed ids: a live capture cannot serve here, because a Stripe-style
 * signature incorporates wall-clock time.
 *
 * Existing subscribers hold `signing_secret` values and verify these headers.
 * A byte that changes without their agreement is a broken integration, not a
 * refactor.
 */
class CaptureGoldensCommand extends Command
{
    protected $signature = 'relay:capture-goldens {--path=tests/goldens}';

    protected $description = 'Render deterministic delivery goldens for outbound webhooks';

    public const FROZEN_TIME = '2026-01-15T12:00:00+00:00';

    public const SECRET = 'golden-relay-secret-not-a-real-one';

    public const EVENT_ID = '01JGOLDEN0EVENT0000000001';

    public function handle(WebhookSigner $signer): int
    {
        $dir = base_path($this->option('path'));

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $timestamp = CarbonImmutable::parse(self::FROZEN_TIME)->getTimestamp();

        $body = json_encode([
            'id' => self::EVENT_ID,
            'type' => 'contact.created',
            'created_at' => CarbonImmutable::parse(self::FROZEN_TIME)->toIso8601String(),
            'data' => ['email' => 'jane.doe@acme.example.test', 'name' => 'Jane Doe'],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $signed = $signer->sign(self::SECRET, $body, $timestamp);

        $golden = [
            '_note' => 'Deterministic golden. Regenerate with `php artisan relay:capture-goldens`.',
            '_frozen_time' => self::FROZEN_TIME,
            'body' => $body,
            'headers' => [
                'X-Webhook-Signature' => $signed['header'],
                'X-Webhook-Event-Id' => self::EVENT_ID,
                'X-Webhook-Event-Type' => 'contact.created',
                'X-Webhook-Timestamp' => (string) $timestamp,
                'Content-Type' => 'application/json',
            ],
        ];

        file_put_contents(
            $dir.'/delivery-default.json',
            json_encode($golden, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n",
        );

        $this->line('  wrote delivery-default.json');
        $this->info('Commit this, then diff after any signing or envelope change.');

        return self::SUCCESS;
    }
}

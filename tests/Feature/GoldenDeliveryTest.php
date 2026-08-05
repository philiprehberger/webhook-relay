<?php

namespace Tests\Feature;

use App\Console\Commands\CaptureGoldensCommand;
use App\Services\WebhookSigner;
use Carbon\CarbonImmutable;
use Tests\TestCase;

/**
 * Plan T-7.2 — D-3 byte-identical regression.
 *
 * Captured before the adoption. Existing subscribers hold signing secrets and
 * verify these headers; a byte that changes without their agreement is a
 * broken integration.
 */
class GoldenDeliveryTest extends TestCase
{
    public function test_delivery_signing_is_byte_identical_to_the_golden(): void
    {
        $golden = json_decode(file_get_contents(base_path('tests/goldens/delivery-default.json')), true);
        $timestamp = CarbonImmutable::parse(CaptureGoldensCommand::FROZEN_TIME)->getTimestamp();

        $signed = (new WebhookSigner)->sign(CaptureGoldensCommand::SECRET, $golden['body'], $timestamp);

        $this->assertSame(
            $golden['headers']['X-Webhook-Signature'],
            $signed['header'],
            'webhook-relay-v0 signature drifted from the golden',
        );
    }

    public function test_the_signed_content_is_still_two_components(): void
    {
        // Standard Webhooks uses {id}.{ts}.{payload}; this native scheme uses
        // {ts}.{payload}. Adopting the contract must not silently switch which
        // construction existing subscribers are verifying against.
        $golden = json_decode(file_get_contents(base_path('tests/goldens/delivery-default.json')), true);
        $timestamp = CarbonImmutable::parse(CaptureGoldensCommand::FROZEN_TIME)->getTimestamp();

        $expected = hash_hmac('sha256', $timestamp.'.'.$golden['body'], CaptureGoldensCommand::SECRET);

        $this->assertSame("t={$timestamp},v1={$expected}", $golden['headers']['X-Webhook-Signature']);
    }

    public function test_a_verifier_accepts_the_golden(): void
    {
        $golden = json_decode(file_get_contents(base_path('tests/goldens/delivery-default.json')), true);

        // Frozen far in the past, so tolerance must be widened deliberately —
        // this asserts the signature, not the freshness window.
        $this->assertTrue((new WebhookSigner)->verify(
            CaptureGoldensCommand::SECRET,
            $golden['body'],
            $golden['headers']['X-Webhook-Signature'],
            toleranceSeconds: PHP_INT_MAX,
        ));
    }
}

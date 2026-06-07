<?php

namespace Tests\Feature;

use App\Jobs\DeliverEventToSubscription;
use App\Models\ApiKey;
use App\Models\Delivery;
use App\Models\Event;
use App\Models\Subscription;
use App\Models\Workspace;
use App\Services\SsrfGuard;
use App\Services\WebhookSigner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeliveryJobTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspace;
    private Subscription $subscription;
    private string $secret;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::create(['name' => 'W', 'slug' => 'w']);
        ApiKey::mint($this->workspace, 'test', 'k');

        $this->secret = Subscription::generateSecret();
        $this->subscription = Subscription::create([
            'workspace_id' => $this->workspace->id,
            'url' => 'https://hooks.example.com/inbound',
            'signing_secret' => $this->secret,
            'event_filter' => '*',
        ]);
    }

    public function test_successful_delivery_records_attempt_and_marks_success(): void
    {
        Http::fake(['hooks.example.com/*' => Http::response('{"ok":true}', 200)]);

        $event = Event::create([
            'workspace_id' => $this->workspace->id,
            'type' => 'order.created',
            'payload' => ['order_id' => 42],
        ]);

        (new DeliverEventToSubscription($event->id, $this->subscription->id))->handle(
            new WebhookSigner(),
            $this->permissiveSsrf(),
        );

        $delivery = Delivery::firstWhere('event_id', $event->id);
        $this->assertSame(Delivery::STATUS_SUCCESS, $delivery->status);
        $this->assertSame(200, $delivery->final_status_code);
        $this->assertSame(1, $delivery->attempts_made);

        $attempt = $delivery->attempts()->first();
        $this->assertSame(1, $attempt->attempt_number);
        $this->assertSame(200, $attempt->response_status);
        $this->assertStringContainsString('"ok":true', $attempt->response_body_snippet);
    }

    public function test_outbound_request_carries_signature_header(): void
    {
        Http::fake(['hooks.example.com/*' => Http::response('ok', 200)]);

        $event = Event::create([
            'workspace_id' => $this->workspace->id,
            'type' => 'order.created',
            'payload' => ['x' => 1],
        ]);

        (new DeliverEventToSubscription($event->id, $this->subscription->id))->handle(
            new WebhookSigner(),
            $this->permissiveSsrf(),
        );

        Http::assertSent(function (HttpRequest $req) {
            return $req->hasHeader('X-Webhook-Signature')
                && $req->hasHeader('X-Webhook-Event-Id')
                && $req->hasHeader('X-Webhook-Event-Type')
                && $req->hasHeader('X-Webhook-Timestamp');
        });
    }

    public function test_5xx_keeps_delivery_pending_and_schedules_retry(): void
    {
        \Illuminate\Support\Facades\Queue::fake();
        Http::fake(['hooks.example.com/*' => Http::response('boom', 503)]);

        $event = Event::create([
            'workspace_id' => $this->workspace->id,
            'type' => 'order.created',
            'payload' => ['x' => 1],
        ]);

        (new DeliverEventToSubscription($event->id, $this->subscription->id))->handle(
            new WebhookSigner(),
            $this->permissiveSsrf(),
        );

        $delivery = Delivery::firstWhere('event_id', $event->id);
        $this->assertSame(Delivery::STATUS_PENDING, $delivery->status);
        $this->assertSame(503, $delivery->final_status_code);
        $this->assertNotNull($delivery->next_attempt_at);

        \Illuminate\Support\Facades\Queue::assertPushed(DeliverEventToSubscription::class);
    }

    public function test_private_ip_url_is_blocked_by_ssrf_guard(): void
    {
        Http::fake();

        $this->subscription->update(['url' => 'https://127.0.0.1/internal']);

        $event = Event::create([
            'workspace_id' => $this->workspace->id,
            'type' => 'order.created',
            'payload' => ['x' => 1],
        ]);

        // Use the real SsrfGuard here — this test is specifically asserting it blocks.
        (new DeliverEventToSubscription($event->id, $this->subscription->id))->handle(
            new WebhookSigner(),
            new SsrfGuard(),
        );

        Http::assertNothingSent();

        $delivery = Delivery::firstWhere('event_id', $event->id);
        // SSRF block is terminal — straight to dead-letter, no retries.
        $this->assertSame(Delivery::STATUS_DEAD, $delivery->status);

        $attempt = $delivery->attempts()->first();
        $this->assertStringStartsWith('ssrf:', $attempt->error_code);
    }

    public function test_inactive_subscription_marks_delivery_dead(): void
    {
        $this->subscription->update(['state' => Subscription::STATE_PAUSED]);
        Http::fake();

        $event = Event::create([
            'workspace_id' => $this->workspace->id,
            'type' => 'order.created',
            'payload' => ['x' => 1],
        ]);

        (new DeliverEventToSubscription($event->id, $this->subscription->id))->handle(
            new WebhookSigner(),
            $this->permissiveSsrf(),
        );

        Http::assertNothingSent();

        $delivery = Delivery::firstWhere('event_id', $event->id);
        $this->assertSame(Delivery::STATUS_DEAD, $delivery->status);
    }

    /**
     * A permissive SsrfGuard for tests that aren't exercising SSRF logic.
     * Real DNS lookups for example.com would fail under WSL/CI and
     * incorrectly trip the guard.
     */
    private function permissiveSsrf(): SsrfGuard
    {
        return new class extends SsrfGuard {
            public function check(string $url): ?string
            {
                return null;
            }
        };
    }
}

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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RetryAndCircuitBreakerTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspace;
    private Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::create(['name' => 'W', 'slug' => 'w']);
        ApiKey::mint($this->workspace, 'test', 'k');

        $this->subscription = Subscription::create([
            'workspace_id' => $this->workspace->id,
            'url' => 'https://hooks.example.com/inbound',
            'signing_secret' => Subscription::generateSecret(),
            'event_filter' => '*',
        ]);
    }

    public function test_backoff_uses_30s_delay_after_first_failure(): void
    {
        Queue::fake();
        Http::fake(['hooks.example.com/*' => Http::response('boom', 500)]);

        $event = $this->makeEvent();

        $this->runJob($event);

        Queue::assertPushed(DeliverEventToSubscription::class);

        $delivery = Delivery::firstWhere('event_id', $event->id);
        $this->assertSame(Delivery::STATUS_PENDING, $delivery->status);
        $this->assertSame(1, $delivery->attempts_made);
        $this->assertEqualsWithDelta(
            now()->addSeconds(30)->getTimestamp(),
            $delivery->next_attempt_at->getTimestamp(),
            5,
        );
    }

    public function test_max_attempts_exhausted_marks_delivery_dead(): void
    {
        Queue::fake();
        Http::fake(['hooks.example.com/*' => Http::response('boom', 500)]);

        $event = $this->makeEvent();
        $delivery = Delivery::create([
            'event_id' => $event->id,
            'subscription_id' => $this->subscription->id,
            'workspace_id' => $this->workspace->id,
            'status' => Delivery::STATUS_PENDING,
            'attempts_made' => DeliverEventToSubscription::MAX_ATTEMPTS - 1,
        ]);

        $this->runJob($event);

        $delivery->refresh();
        $this->assertSame(Delivery::STATUS_DEAD, $delivery->status);
        $this->assertSame(DeliverEventToSubscription::MAX_ATTEMPTS, $delivery->attempts_made);
    }

    public function test_4xx_marks_dead_immediately_no_retry(): void
    {
        Queue::fake();
        Http::fake(['hooks.example.com/*' => Http::response('bad', 400)]);

        $event = $this->makeEvent();

        $this->runJob($event);

        $delivery = Delivery::firstWhere('event_id', $event->id);
        $this->assertSame(Delivery::STATUS_DEAD, $delivery->status);
        $this->assertSame(400, $delivery->final_status_code);

        Queue::assertNotPushed(DeliverEventToSubscription::class);
    }

    public function test_2xx_resets_circuit_breaker(): void
    {
        Http::fake(['hooks.example.com/*' => Http::response('ok', 200)]);

        $this->subscription->update(['consecutive_failures' => 5]);

        $this->runJob($this->makeEvent());

        $this->subscription->refresh();
        $this->assertSame(0, $this->subscription->consecutive_failures);
    }

    public function test_each_failure_increments_consecutive_failures(): void
    {
        Queue::fake();
        Http::fake(['hooks.example.com/*' => Http::response('nope', 500)]);

        $this->runJob($this->makeEvent());
        $this->runJob($this->makeEvent());
        $this->runJob($this->makeEvent());

        $this->subscription->refresh();
        $this->assertSame(3, $this->subscription->consecutive_failures);
        $this->assertTrue($this->subscription->isActive());
    }

    public function test_circuit_breaker_pauses_subscription_at_threshold(): void
    {
        Queue::fake();
        Http::fake(['hooks.example.com/*' => Http::response('nope', 500)]);

        $this->subscription->update([
            'consecutive_failures' => DeliverEventToSubscription::CIRCUIT_BREAKER_THRESHOLD - 1,
        ]);

        $this->runJob($this->makeEvent());

        $this->subscription->refresh();
        $this->assertSame(Subscription::STATE_PAUSED, $this->subscription->state);
        $this->assertNotNull($this->subscription->paused_at);
    }

    private function makeEvent(): Event
    {
        return Event::create([
            'workspace_id' => $this->workspace->id,
            'type' => 'order.created',
            'payload' => ['x' => 1],
        ]);
    }

    private function runJob(Event $event): void
    {
        (new DeliverEventToSubscription($event->id, $this->subscription->id))->handle(
            new WebhookSigner(),
            $this->permissiveSsrf(),
        );
    }

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

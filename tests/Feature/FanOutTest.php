<?php

namespace Tests\Feature;

use App\Jobs\DeliverEventToSubscription;
use App\Models\ApiKey;
use App\Models\Subscription;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FanOutTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspace;
    private string $key;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::create(['name' => 'W', 'slug' => 'w']);
        [, $this->key] = ApiKey::mint($this->workspace, 'test', 'k');
    }

    public function test_ingest_with_no_subscriptions_dispatches_zero_jobs(): void
    {
        Queue::fake();

        $this->withToken($this->key)
            ->postJson('/v1/events', ['type' => 'order.created', 'payload' => ['x' => 1]])
            ->assertStatus(202);

        Queue::assertNothingPushed();
    }

    public function test_ingest_dispatches_one_job_per_matching_subscription(): void
    {
        Queue::fake();

        Subscription::create([
            'workspace_id' => $this->workspace->id,
            'url' => 'https://a.example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
            'event_filter' => 'order.*',
        ]);
        Subscription::create([
            'workspace_id' => $this->workspace->id,
            'url' => 'https://b.example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
            'event_filter' => 'order.created',
        ]);
        Subscription::create([
            'workspace_id' => $this->workspace->id,
            'url' => 'https://c.example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
            'event_filter' => 'user.*',
        ]);

        $this->withToken($this->key)
            ->postJson('/v1/events', ['type' => 'order.created', 'payload' => ['x' => 1]])
            ->assertStatus(202);

        Queue::assertPushed(DeliverEventToSubscription::class, 2);
    }

    public function test_ingest_creates_pending_delivery_rows_per_match(): void
    {
        Queue::fake();

        $subA = Subscription::create([
            'workspace_id' => $this->workspace->id,
            'url' => 'https://a.example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
            'event_filter' => '*',
        ]);

        $response = $this->withToken($this->key)
            ->postJson('/v1/events', ['type' => 'order.created', 'payload' => ['x' => 1]]);

        $response->assertStatus(202);
        $this->assertDatabaseHas('deliveries', [
            'event_id' => $response->json('id'),
            'subscription_id' => $subA->id,
            'status' => 'pending',
        ]);
    }

    public function test_paused_subscription_is_excluded_from_fan_out(): void
    {
        Queue::fake();

        Subscription::create([
            'workspace_id' => $this->workspace->id,
            'url' => 'https://paused.example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
            'event_filter' => '*',
            'state' => Subscription::STATE_PAUSED,
        ]);

        $this->withToken($this->key)
            ->postJson('/v1/events', ['type' => 'anything', 'payload' => ['x' => 1]])
            ->assertStatus(202);

        Queue::assertNothingPushed();
        $this->assertDatabaseCount('deliveries', 0);
    }
}

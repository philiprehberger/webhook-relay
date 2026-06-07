<?php

namespace Tests\Feature;

use App\Jobs\DeliverEventToSubscription;
use App\Models\ApiKey;
use App\Models\Delivery;
use App\Models\Event;
use App\Models\Subscription;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DeadLetterApiTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspace;
    private string $key;
    private Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::create(['name' => 'W', 'slug' => 'w']);
        [, $this->key] = ApiKey::mint($this->workspace, 'test', 'k');
        $this->subscription = Subscription::create([
            'workspace_id' => $this->workspace->id,
            'url' => 'https://example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
        ]);
    }

    public function test_dead_letters_index_returns_only_dead_status(): void
    {
        $this->seedDelivery(status: Delivery::STATUS_SUCCESS);
        $this->seedDelivery(status: Delivery::STATUS_DEAD);
        $this->seedDelivery(status: Delivery::STATUS_DEAD);

        $response = $this->withToken($this->key)->getJson('/v1/dead-letters');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
        foreach ($response->json('data') as $row) {
            $this->assertSame('dead', $row['status']);
        }
    }

    public function test_replay_revives_dead_delivery_and_dispatches_job(): void
    {
        Queue::fake();

        $delivery = $this->seedDelivery(status: Delivery::STATUS_DEAD);

        $response = $this->withToken($this->key)
            ->postJson("/v1/dead-letters/{$delivery->id}/replay");

        $response->assertOk();
        $delivery->refresh();
        $this->assertSame(Delivery::STATUS_PENDING, $delivery->status);
        Queue::assertPushed(DeliverEventToSubscription::class);
    }

    public function test_replay_returns_409_for_non_dead_delivery(): void
    {
        $delivery = $this->seedDelivery(status: Delivery::STATUS_SUCCESS);

        $this->withToken($this->key)
            ->postJson("/v1/dead-letters/{$delivery->id}/replay")
            ->assertStatus(409);
    }

    public function test_manual_retry_endpoint_dispatches_new_job(): void
    {
        Queue::fake();

        $delivery = $this->seedDelivery(status: Delivery::STATUS_DEAD);

        $response = $this->withToken($this->key)
            ->postJson("/v1/deliveries/{$delivery->id}/retry");

        $response->assertOk();
        Queue::assertPushed(DeliverEventToSubscription::class);

        $delivery->refresh();
        $this->assertSame(Delivery::STATUS_PENDING, $delivery->status);
    }

    private function seedDelivery(string $status): Delivery
    {
        $event = Event::create([
            'workspace_id' => $this->workspace->id,
            'type' => 'order.created',
            'payload' => ['x' => 1],
        ]);

        return Delivery::create([
            'event_id' => $event->id,
            'subscription_id' => $this->subscription->id,
            'workspace_id' => $this->workspace->id,
            'status' => $status,
            'attempts_made' => 1,
            'completed_at' => $status === Delivery::STATUS_DEAD ? now() : null,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Delivery;
use App\Models\DeliveryAttempt;
use App\Models\Event;
use App\Models\Subscription;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveriesApiTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspaceA;
    private Workspace $workspaceB;
    private string $keyA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspaceA = Workspace::create(['name' => 'A', 'slug' => 'a']);
        $this->workspaceB = Workspace::create(['name' => 'B', 'slug' => 'b']);
        [, $this->keyA] = ApiKey::mint($this->workspaceA, 'test', 'a');
    }

    public function test_index_returns_workspace_deliveries(): void
    {
        $this->seedDelivery($this->workspaceA);
        $this->seedDelivery($this->workspaceA);
        $this->seedDelivery($this->workspaceB);

        $response = $this->withToken($this->keyA)->getJson('/v1/deliveries');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_index_filters_by_status(): void
    {
        $this->seedDelivery($this->workspaceA, status: 'success');
        $this->seedDelivery($this->workspaceA, status: 'failed');
        $this->seedDelivery($this->workspaceA, status: 'failed');

        $response = $this->withToken($this->keyA)->getJson('/v1/deliveries?status=failed');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_show_includes_attempts_timeline(): void
    {
        $delivery = $this->seedDelivery($this->workspaceA, status: 'failed');

        DeliveryAttempt::create([
            'delivery_id' => $delivery->id,
            'attempt_number' => 1,
            'request_signature' => 't=1,v1=abc',
            'response_status' => 500,
            'response_headers' => ['Content-Type' => 'text/plain'],
            'response_body_snippet' => 'boom',
            'latency_ms' => 1234,
            'error_code' => null,
            'attempted_at' => now(),
        ]);

        $response = $this->withToken($this->keyA)->getJson("/v1/deliveries/{$delivery->id}");

        $response->assertOk();
        $response->assertJsonStructure(['id', 'event_id', 'subscription_id', 'status', 'attempts']);
        $this->assertCount(1, $response->json('attempts'));
        $this->assertSame(500, $response->json('attempts.0.response_status'));
    }

    public function test_show_404s_across_workspaces(): void
    {
        $delivery = $this->seedDelivery($this->workspaceB);

        $this->withToken($this->keyA)->getJson("/v1/deliveries/{$delivery->id}")
            ->assertStatus(404);
    }

    private function seedDelivery(Workspace $workspace, string $status = 'success'): Delivery
    {
        $event = Event::create([
            'workspace_id' => $workspace->id,
            'type' => 'order.created',
            'payload' => ['seeded' => true],
        ]);
        $sub = Subscription::create([
            'workspace_id' => $workspace->id,
            'url' => 'https://example.com/hooks',
            'signing_secret' => Subscription::generateSecret(),
        ]);

        return Delivery::create([
            'event_id' => $event->id,
            'subscription_id' => $sub->id,
            'workspace_id' => $workspace->id,
            'status' => $status,
            'attempts_made' => 1,
            'completed_at' => now(),
        ]);
    }
}

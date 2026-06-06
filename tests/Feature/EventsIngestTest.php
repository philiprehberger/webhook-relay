<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Event;
use App\Models\IdempotencyRecord;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsIngestTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspace;
    private string $key;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::create([
            'name' => 'Test Workspace',
            'slug' => 'test-workspace',
        ]);

        [, $this->key] = ApiKey::mint($this->workspace, 'test', 'test key');
    }

    public function test_post_without_auth_returns_401_problem_json(): void
    {
        $response = $this->postJson('/v1/events', [
            'type' => 'order.created',
            'payload' => ['order_id' => 1],
        ]);

        $response->assertStatus(401);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJsonPath('status', 401);
        $response->assertJsonStructure(['type', 'title', 'status']);
    }

    public function test_post_with_invalid_key_returns_401(): void
    {
        $response = $this->withToken('whk_test_invalid_garbage')->postJson('/v1/events', [
            'type' => 'order.created',
            'payload' => ['order_id' => 1],
        ]);

        $response->assertStatus(401);
    }

    public function test_post_with_valid_key_creates_event_and_returns_202(): void
    {
        $response = $this->withToken($this->key)->postJson('/v1/events', [
            'type' => 'order.created',
            'payload' => ['order_id' => 42, 'total_cents' => 9900],
        ]);

        $response->assertStatus(202);
        $response->assertJsonStructure(['id', 'type', 'payload', 'created_at', 'deliveries_summary']);
        $response->assertJsonPath('type', 'order.created');
        $response->assertJsonPath('payload.order_id', 42);
        $response->assertJsonPath('deliveries_summary.total', 0);

        $this->assertDatabaseCount('events', 1);
        $event = Event::firstOrFail();
        $this->assertSame($this->workspace->id, $event->workspace_id);
    }

    public function test_post_validates_event_type_format(): void
    {
        $response = $this->withToken($this->key)->postJson('/v1/events', [
            'type' => 'Invalid Type With Spaces',
            'payload' => ['x' => 1],
        ]);

        $response->assertStatus(400);
    }

    public function test_post_requires_payload_array(): void
    {
        $response = $this->withToken($this->key)->postJson('/v1/events', [
            'type' => 'order.created',
        ]);

        $response->assertStatus(400);
        $response->assertJsonPath('errors.payload.0', 'The payload field is required.');
    }

    public function test_idempotency_key_returns_cached_response_on_replay(): void
    {
        $payload = ['type' => 'order.created', 'payload' => ['order_id' => 7]];

        $first = $this->withToken($this->key)
            ->withHeaders(['Idempotency-Key' => 'replay-me-1'])
            ->postJson('/v1/events', $payload);

        $first->assertStatus(202);
        $firstId = $first->json('id');

        $second = $this->withToken($this->key)
            ->withHeaders(['Idempotency-Key' => 'replay-me-1'])
            ->postJson('/v1/events', $payload);

        $second->assertStatus(202);
        $this->assertSame($firstId, $second->json('id'));

        $this->assertDatabaseCount('events', 1);
        $this->assertDatabaseCount('idempotency_records', 1);
    }

    public function test_idempotency_key_with_different_payload_returns_409(): void
    {
        $this->withToken($this->key)
            ->withHeaders(['Idempotency-Key' => 'conflict-key'])
            ->postJson('/v1/events', ['type' => 'order.created', 'payload' => ['order_id' => 1]])
            ->assertStatus(202);

        $response = $this->withToken($this->key)
            ->withHeaders(['Idempotency-Key' => 'conflict-key'])
            ->postJson('/v1/events', ['type' => 'order.created', 'payload' => ['order_id' => 999]]);

        $response->assertStatus(409);
        $response->assertJsonPath('status', 409);
    }

    public function test_expired_idempotency_record_is_replaced_not_replayed(): void
    {
        $this->withToken($this->key)
            ->withHeaders(['Idempotency-Key' => 'old-key'])
            ->postJson('/v1/events', ['type' => 'order.created', 'payload' => ['n' => 1]])
            ->assertStatus(202);

        // Force the record to be expired.
        IdempotencyRecord::query()->update(['expires_at' => now()->subDay()]);

        $response = $this->withToken($this->key)
            ->withHeaders(['Idempotency-Key' => 'old-key'])
            ->postJson('/v1/events', ['type' => 'order.created', 'payload' => ['n' => 1]]);

        $response->assertStatus(202);
        $this->assertDatabaseCount('events', 2);
        $this->assertDatabaseCount('idempotency_records', 1); // old deleted, new written
    }
}

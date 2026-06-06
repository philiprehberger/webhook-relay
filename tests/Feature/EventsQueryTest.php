<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Event;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsQueryTest extends TestCase
{
    use RefreshDatabase;

    private Workspace $workspaceA;
    private Workspace $workspaceB;
    private string $keyA;
    private string $keyB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspaceA = Workspace::create(['name' => 'A', 'slug' => 'a']);
        $this->workspaceB = Workspace::create(['name' => 'B', 'slug' => 'b']);
        [, $this->keyA] = ApiKey::mint($this->workspaceA, 'test', 'a');
        [, $this->keyB] = ApiKey::mint($this->workspaceB, 'test', 'b');
    }

    public function test_index_returns_only_events_in_callers_workspace(): void
    {
        $this->seedEvent($this->workspaceA, 'order.created');
        $this->seedEvent($this->workspaceA, 'order.shipped');
        $this->seedEvent($this->workspaceB, 'order.created');

        $response = $this->withToken($this->keyA)->getJson('/v1/events');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_index_filters_by_type(): void
    {
        $this->seedEvent($this->workspaceA, 'order.created');
        $this->seedEvent($this->workspaceA, 'order.shipped');
        $this->seedEvent($this->workspaceA, 'order.created');

        $response = $this->withToken($this->keyA)->getJson('/v1/events?type=order.shipped');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertSame('order.shipped', $response->json('data.0.type'));
    }

    public function test_show_returns_event_in_workspace(): void
    {
        $event = $this->seedEvent($this->workspaceA, 'order.created');

        $response = $this->withToken($this->keyA)->getJson("/v1/events/{$event->id}");

        $response->assertOk();
        $response->assertJsonPath('id', $event->id);
        $response->assertJsonPath('deliveries_summary.total', 0);
    }

    public function test_show_returns_404_when_event_belongs_to_other_workspace(): void
    {
        $event = $this->seedEvent($this->workspaceB, 'order.created');

        $response = $this->withToken($this->keyA)->getJson("/v1/events/{$event->id}");

        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/problem+json');
    }

    public function test_show_returns_404_for_unknown_id(): void
    {
        $response = $this->withToken($this->keyA)->getJson('/v1/events/01J0NEXISTENT0000000000000');

        $response->assertStatus(404);
    }

    private function seedEvent(Workspace $workspace, string $type): Event
    {
        return Event::create([
            'workspace_id' => $workspace->id,
            'type' => $type,
            'payload' => ['seeded' => true],
        ]);
    }
}

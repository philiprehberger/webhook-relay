<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ProblemResponse;
use App\Models\Delivery;
use App\Models\Event;
use App\Models\Workspace;
use Illuminate\Http\Request;

/**
 * Plan 7.6 — `GET /v1/traces/{trace_id}`.
 *
 * The endpoint that answers the question this fleet could not answer before:
 * "which delivery came from this submission?"
 *
 * Two constraints, both load-bearing:
 *
 * 1. WORKSPACE SCOPED. Trace ids are 128-bit random so enumeration is
 *    impractical, but "hard to guess" is not authorization. Every query is
 *    filtered by the caller's workspace, and a trace belonging to another
 *    tenant is indistinguishable from one that does not exist.
 *
 * 2. METADATA ONLY, for every trace class (SPEC §3.4). Type, timestamps,
 *    status codes, attempt counts, byte sizes, outcome — never request or
 *    response bodies. This is a trace-shaped endpoint, so the rule applies
 *    regardless of whether the caller could read the same payload through
 *    `GET /v1/events`. Adding a second, laxer path to the same data is how
 *    exposure rules get quietly undone.
 */
class TracesController extends Controller
{
    public function show(Request $request, string $traceId)
    {
        /** @var Workspace $workspace */
        $workspace = $request->attributes->get('workspace');

        if (preg_match('/^[0-9a-f]{32}$/', $traceId) !== 1) {
            return new ProblemResponse(
                status: 400,
                title: 'Invalid trace id',
                detail: 'A trace id is 32 lowercase hex characters.',
            );
        }

        $events = Event::query()
            ->where('workspace_id', $workspace->id)
            ->where('trace_id', $traceId)
            ->orderBy('created_at')
            ->get();

        $deliveries = Delivery::query()
            ->where('workspace_id', $workspace->id)
            ->where('trace_id', $traceId)
            ->with(['attempts' => fn ($q) => $q->orderBy('attempt_number')])
            ->orderBy('created_at')
            ->get();

        if ($events->isEmpty() && $deliveries->isEmpty()) {
            // Deliberately identical to the response for another tenant's
            // trace: existence is itself information.
            return new ProblemResponse(
                status: 404,
                title: 'Not found',
                detail: 'No trace with that id in this workspace.',
            );
        }

        return response()->json([
            'trace_id' => $traceId,
            'events' => $events->map(fn (Event $event) => [
                'id' => $event->id,
                'type' => $event->type,
                'correlation_id' => $event->correlation_id,
                'causation_id' => $event->causation_id,
                'trace_class' => $event->trace_class,
                'created_at' => $event->created_at?->toIso8601String(),
                // Size, not content. Enough to see a payload was large without
                // becoming a second way to read it.
                'payload_bytes' => strlen((string) json_encode($event->payload)),
            ])->all(),
            'deliveries' => $deliveries->map(fn (Delivery $delivery) => [
                'id' => $delivery->id,
                'event_id' => $delivery->event_id,
                'subscription_id' => $delivery->subscription_id,
                'status' => $delivery->status,
                'attempts_made' => $delivery->attempts_made,
                'final_status_code' => $delivery->final_status_code,
                'completed_at' => $delivery->completed_at?->toIso8601String(),
                'attempts' => $delivery->attempts->map(fn ($attempt) => [
                    'attempt_number' => $attempt->attempt_number,
                    'response_status' => $attempt->response_status,
                    'duration_ms' => $attempt->latency_ms,
                    'created_at' => $attempt->created_at?->toIso8601String(),
                    // NOT response_body_snippet: that field exists on the
                    // delivery-attempt resource, and this endpoint does not
                    // become a second route to it.
                ])->all(),
            ])->all(),
        ]);
    }
}

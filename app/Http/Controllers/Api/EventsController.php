<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Responses\ProblemResponse;
use App\Jobs\DeliverEventToSubscription;
use App\Models\Delivery;
use App\Models\Event;
use App\Models\IdempotencyRecord;
use App\Models\Workspace;
use App\Services\SubscriptionMatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhilipRehberger\Interchange\Tracing\TraceScope;

class EventsController extends Controller
{
    private const IDEMPOTENCY_WINDOW_HOURS = 24;
    private const MAX_PAYLOAD_BYTES = 262144; // 256 KB

    public function store(Request $request): JsonResponse|ProblemResponse
    {
        /** @var Workspace $workspace */
        $workspace = $request->attributes->get('workspace');

        $validated = $request->validate([
            'type' => ['required', 'string', 'regex:/^[a-z0-9._-]{1,128}$/'],
            'payload' => ['required', 'array'],
            // Plan 7.3 — accepted via envelope OR header; generated when absent.
            'correlation_id' => ['sometimes', 'nullable', 'string', 'max:64'],
            'causation_id' => ['sometimes', 'nullable', 'string', 'max:64'],
        ]);

        // Header wins over body only when the body omits it: a caller that
        // states it explicitly means it. Generated when neither is present, so
        // every event is correlatable even from a client that knows nothing
        // about the contract.
        $correlationId = $validated['correlation_id']
            ?? $request->header('X-Correlation-Id')
            ?? (string) Str::ulid();

        $traceContext = TraceScope::current();

        $rawPayload = json_encode($validated['payload']);
        if ($rawPayload === false || strlen($rawPayload) > self::MAX_PAYLOAD_BYTES) {
            return new ProblemResponse(
                status: 400,
                title: 'Invalid request',
                detail: 'Payload exceeds the 256 KB limit.',
            );
        }

        $idempotencyKey = $this->extractIdempotencyKey($request);
        $fingerprint = $idempotencyKey !== null
            ? $this->fingerprint($workspace->id, $validated['type'], $rawPayload)
            : null;

        if ($idempotencyKey !== null) {
            $cached = IdempotencyRecord::where('workspace_id', $workspace->id)
                ->where('key', $idempotencyKey)
                ->first();

            if ($cached !== null && ! $cached->isExpired()) {
                if ($cached->request_fingerprint !== $fingerprint) {
                    return new ProblemResponse(
                        status: 409,
                        title: 'Conflict',
                        detail: 'Idempotency-Key was already used with a different payload.',
                    );
                }

                return new JsonResponse(
                    data: $cached->response_body,
                    status: $cached->response_status,
                );
            }

            if ($cached !== null) {
                $cached->delete();
            }
        }

        return DB::transaction(function () use ($workspace, $validated, $idempotencyKey, $fingerprint, $request, $correlationId, $traceContext) {
            $event = Event::create([
                'workspace_id' => $workspace->id,
                'type' => $validated['type'],
                'payload' => $validated['payload'],
                'idempotency_key' => $idempotencyKey,
                'source_ip' => $request->ip(),
                'correlation_id' => $correlationId,
                'causation_id' => $validated['causation_id'] ?? $request->header('X-Causation-Id'),
                'trace_id' => $traceContext?->traceId,
                // Advisory only (SPEC §3.4): recorded so a scenario run is
                // distinguishable, never consulted for storage or exposure.
                // Fail closed, and *normalise* rather than merely defaulting.
                //
                // `?? 'production'` only handled an absent member: a caller
                // sending `mnl_class=Scenario` or ` scenario ` had that stored
                // verbatim, producing a value that is neither class and that a
                // later `!== 'production'` check would read as permissive.
                // Anything that is not exactly `scenario` is production.
                //
                // This duplicates PhilipRehberger\Interchange\Tracing\TraceClass,
                // which exists in the package source but not in the released
                // v0.2.0 pinned here. Replace this with TraceClass::fromState()
                // once a release carrying it is cut — the package's own docblock
                // makes the point that a fail-closed rule reimplemented per
                // service is a fail-closed rule with per-service exceptions.
                'trace_class' => $traceContext?->state?->get('mnl_class') === 'scenario'
                    ? 'scenario'
                    : 'production',
            ]);

            $this->dispatchFanOut($event);

            $responseBody = (new EventResource($event))->resolve();

            if ($idempotencyKey !== null) {
                IdempotencyRecord::create([
                    'workspace_id' => $workspace->id,
                    'key' => $idempotencyKey,
                    'request_fingerprint' => $fingerprint,
                    'response_body' => $responseBody,
                    'response_status' => 202,
                    'event_id' => $event->id,
                    'expires_at' => now()->addHours(self::IDEMPOTENCY_WINDOW_HOURS),
                ]);
            }

            return new JsonResponse(data: $responseBody, status: 202);
        });
    }

    /**
     * For every matching subscription, create a pending Delivery row and
     * enqueue a DeliverEventToSubscription job. Dispatch is gated on
     * afterCommit so we never enqueue work for a rolled-back event.
     */
    private function dispatchFanOut(Event $event): void
    {
        $subscriptions = app(SubscriptionMatcher::class)->matchingSubscriptions($event);

        foreach ($subscriptions as $subscription) {
            Delivery::firstOrCreate(
                ['event_id' => $event->id, 'subscription_id' => $subscription->id],
                [
                    'workspace_id' => $event->workspace_id,
                    'status' => Delivery::STATUS_PENDING,
                    'attempts_made' => 0,
                ],
            );

            DeliverEventToSubscription::dispatch($event->id, $subscription->id)
                ->afterCommit();
        }
    }

    public function index(Request $request): JsonResponse
    {
        /** @var Workspace $workspace */
        $workspace = $request->attributes->get('workspace');

        $validated = $request->validate([
            'type' => ['nullable', 'string'],
            'created_after' => ['nullable', 'date'],
            'cursor' => ['nullable', 'string'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Event::where('workspace_id', $workspace->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if (! empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        if (! empty($validated['created_after'])) {
            $query->where('created_at', '>', $validated['created_after']);
        }

        $limit = $validated['limit'] ?? 25;
        $page = $query->cursorPaginate(perPage: $limit, cursorName: 'cursor', cursor: $validated['cursor'] ?? null);

        return response()->json([
            'data' => EventResource::collection($page->items())->resolve(),
            'next_cursor' => $page->nextCursor()?->encode(),
        ]);
    }

    public function show(Request $request, string $id): JsonResponse|ProblemResponse
    {
        /** @var Workspace $workspace */
        $workspace = $request->attributes->get('workspace');

        $event = Event::where('workspace_id', $workspace->id)
            ->where('id', $id)
            ->first();

        if ($event === null) {
            return new ProblemResponse(
                status: 404,
                title: 'Not found',
                detail: 'No event with that id exists in this workspace.',
            );
        }

        return response()->json((new EventResource($event))->resolve());
    }

    private function extractIdempotencyKey(Request $request): ?string
    {
        $raw = $request->headers->get('Idempotency-Key', '');

        if (! is_string($raw) || $raw === '') {
            return null;
        }

        $trimmed = trim($raw);
        if ($trimmed === '' || strlen($trimmed) > 255) {
            return null;
        }

        return $trimmed;
    }

    private function fingerprint(string $workspaceId, string $type, string $rawPayload): string
    {
        return hash('sha256', $workspaceId.'|'.$type.'|'.$rawPayload);
    }
}

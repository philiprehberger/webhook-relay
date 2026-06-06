<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Responses\ProblemResponse;
use App\Models\Event;
use App\Models\IdempotencyRecord;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        ]);

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

        return DB::transaction(function () use ($workspace, $validated, $idempotencyKey, $fingerprint, $request) {
            $event = Event::create([
                'workspace_id' => $workspace->id,
                'type' => $validated['type'],
                'payload' => $validated['payload'],
                'idempotency_key' => $idempotencyKey,
                'source_ip' => $request->ip(),
            ]);

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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryResource;
use App\Http\Responses\ProblemResponse;
use App\Jobs\DeliverEventToSubscription;
use App\Models\Delivery;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Operator-facing view of deliveries with status=dead. Deliveries can be
 * dead because:
 *  - The subscriber returned 4xx (not retried).
 *  - Retries exhausted on 5xx / timeout / connection error.
 *  - The subscription was paused / disabled.
 *  - SSRF guard blocked the URL.
 */
class DeadLettersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var Workspace $workspace */
        $workspace = $request->attributes->get('workspace');

        $validated = $request->validate([
            'subscription_id' => ['nullable', 'string'],
            'since' => ['nullable', 'date'],
            'cursor' => ['nullable', 'string'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Delivery::where('workspace_id', $workspace->id)
            ->where('status', Delivery::STATUS_DEAD)
            ->orderByDesc('completed_at')
            ->orderByDesc('id');

        if (! empty($validated['subscription_id'])) {
            $query->where('subscription_id', $validated['subscription_id']);
        }
        if (! empty($validated['since'])) {
            $query->where('completed_at', '>=', $validated['since']);
        }

        $limit = $validated['limit'] ?? 25;
        $page = $query->cursorPaginate(perPage: $limit, cursorName: 'cursor', cursor: $validated['cursor'] ?? null);

        return response()->json([
            'data' => collect($page->items())
                ->map(fn (Delivery $d) => (new DeliveryResource($d, includeAttempts: false))->resolve())
                ->all(),
            'next_cursor' => $page->nextCursor()?->encode(),
        ]);
    }

    public function replay(Request $request, string $id): JsonResponse|ProblemResponse
    {
        /** @var Workspace $workspace */
        $workspace = $request->attributes->get('workspace');

        $delivery = Delivery::where('workspace_id', $workspace->id)
            ->where('id', $id)
            ->first();

        if ($delivery === null) {
            return new ProblemResponse(
                status: 404,
                title: 'Not found',
                detail: 'No delivery with that id exists in this workspace.',
            );
        }

        if ($delivery->status !== Delivery::STATUS_DEAD) {
            return new ProblemResponse(
                status: 409,
                title: 'Conflict',
                detail: 'Only dead-lettered deliveries can be replayed here. Use POST /v1/deliveries/{id}/retry to retry deliveries in other states.',
            );
        }

        $delivery->update([
            'status' => Delivery::STATUS_PENDING,
            'next_attempt_at' => null,
            'completed_at' => null,
        ]);

        DeliverEventToSubscription::dispatch($delivery->event_id, $delivery->subscription_id);

        return response()->json(
            (new DeliveryResource($delivery->refresh(), includeAttempts: true))->resolve(),
        );
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryResource;
use App\Http\Responses\ProblemResponse;
use App\Models\Delivery;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveriesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var Workspace $workspace */
        $workspace = $request->attributes->get('workspace');

        $validated = $request->validate([
            'event_id' => ['nullable', 'string'],
            'subscription_id' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:pending,success,failed,dead'],
            'cursor' => ['nullable', 'string'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Delivery::where('workspace_id', $workspace->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        foreach (['event_id', 'subscription_id', 'status'] as $filter) {
            if (! empty($validated[$filter])) {
                $query->where($filter, $validated[$filter]);
            }
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

    public function show(Request $request, string $id): JsonResponse|ProblemResponse
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

        return response()->json(
            (new DeliveryResource($delivery, includeAttempts: true))->resolve(),
        );
    }
}

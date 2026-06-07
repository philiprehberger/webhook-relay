<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use App\Http\Responses\ProblemResponse;
use App\Models\Subscription;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        /** @var Workspace $workspace */
        $workspace = $request->attributes->get('workspace');

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'url' => ['required', 'url', 'starts_with:https://', 'max:2048'],
            'event_filter' => ['nullable', 'string', 'max:128', 'regex:/^[a-z0-9._*-]+$/'],
        ]);

        $plaintextSecret = Subscription::generateSecret();

        $subscription = Subscription::create([
            'workspace_id' => $workspace->id,
            'name' => $validated['name'] ?? null,
            'url' => $validated['url'],
            'signing_secret' => $plaintextSecret,
            'event_filter' => $validated['event_filter'] ?? '*',
            'state' => Subscription::STATE_ACTIVE,
        ]);

        return new JsonResponse(
            data: (new SubscriptionResource($subscription))->withSecret($plaintextSecret),
            status: 201,
        );
    }

    public function index(Request $request): JsonResponse
    {
        /** @var Workspace $workspace */
        $workspace = $request->attributes->get('workspace');

        $validated = $request->validate([
            'cursor' => ['nullable', 'string'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'state' => ['nullable', 'string', 'in:active,paused,disabled'],
        ]);

        $query = Subscription::where('workspace_id', $workspace->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if (! empty($validated['state'])) {
            $query->where('state', $validated['state']);
        }

        $limit = $validated['limit'] ?? 25;
        $page = $query->cursorPaginate(perPage: $limit, cursorName: 'cursor', cursor: $validated['cursor'] ?? null);

        return response()->json([
            'data' => SubscriptionResource::collection($page->items())->resolve(),
            'next_cursor' => $page->nextCursor()?->encode(),
        ]);
    }

    public function show(Request $request, string $id): JsonResponse|ProblemResponse
    {
        $subscription = $this->findScoped($request, $id);
        if ($subscription === null) {
            return $this->notFound();
        }

        return response()->json((new SubscriptionResource($subscription))->resolve());
    }

    public function update(Request $request, string $id): JsonResponse|ProblemResponse
    {
        $subscription = $this->findScoped($request, $id);
        if ($subscription === null) {
            return $this->notFound();
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'starts_with:https://', 'max:2048'],
            'event_filter' => ['nullable', 'string', 'max:128', 'regex:/^[a-z0-9._*-]+$/'],
        ]);

        $subscription->fill(array_filter($validated, fn ($v) => $v !== null));
        $subscription->save();

        return response()->json((new SubscriptionResource($subscription))->resolve());
    }

    public function destroy(Request $request, string $id): JsonResponse|ProblemResponse
    {
        $subscription = $this->findScoped($request, $id);
        if ($subscription === null) {
            return $this->notFound();
        }

        $subscription->delete();

        return new JsonResponse(status: 204);
    }

    public function pause(Request $request, string $id): JsonResponse|ProblemResponse
    {
        $subscription = $this->findScoped($request, $id);
        if ($subscription === null) {
            return $this->notFound();
        }

        $subscription->pause();

        return response()->json((new SubscriptionResource($subscription))->resolve());
    }

    public function resume(Request $request, string $id): JsonResponse|ProblemResponse
    {
        $subscription = $this->findScoped($request, $id);
        if ($subscription === null) {
            return $this->notFound();
        }

        $subscription->resume();

        return response()->json((new SubscriptionResource($subscription))->resolve());
    }

    public function rotateSecret(Request $request, string $id): JsonResponse|ProblemResponse
    {
        $subscription = $this->findScoped($request, $id);
        if ($subscription === null) {
            return $this->notFound();
        }

        $plaintextSecret = $subscription->rotateSecret();

        return response()->json((new SubscriptionResource($subscription))->withSecret($plaintextSecret));
    }

    private function findScoped(Request $request, string $id): ?Subscription
    {
        /** @var Workspace $workspace */
        $workspace = $request->attributes->get('workspace');

        return Subscription::where('workspace_id', $workspace->id)
            ->where('id', $id)
            ->first();
    }

    private function notFound(): ProblemResponse
    {
        return new ProblemResponse(
            status: 404,
            title: 'Not found',
            detail: 'No subscription with that id exists in this workspace.',
        );
    }
}

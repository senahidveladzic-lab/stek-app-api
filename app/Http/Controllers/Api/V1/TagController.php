<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class TagController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $household = $request->user()->household;

        if (! $household) {
            return response()->json(['message' => 'User is not assigned to a household.'], 422);
        }

        Gate::authorize('viewAny', Tag::class);

        $tags = $household->tags()
            ->orderBy('name')
            ->get();

        return TagResource::collection($tags);
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = $request->user()->household->tags()->create($request->validated());

        return (new TagResource($tag))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateTagRequest $request, Tag $tag): TagResource
    {
        $tag->update($request->validated());

        return new TagResource($tag);
    }

    public function destroy(Request $request, Tag $tag): JsonResponse
    {
        Gate::authorize('delete', $tag);

        $tag->expenses()->detach();
        $tag->delete();

        return response()->json(null, 204);
    }
}

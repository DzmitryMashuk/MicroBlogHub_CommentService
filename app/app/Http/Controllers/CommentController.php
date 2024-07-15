<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\Cache\RedisCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(private RedisCacheService $redisCacheService) {
    }

    public function index(): JsonResponse
    {
        $comments = $this->redisCacheService->get(config('redis_keys.comments'));

        if ($comments) {
            return response()->json(json_decode($comments, true));
        }

        $comments = Comment::all();

        $this->redisCacheService->set(config('redis_keys.comments'), json_encode($comments));

        return response()->json($comments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'post_id'   => 'required|integer',
            'user_id'   => 'required|integer',
            'content'   => 'required|string',
            'status'    => 'integer',
            'parent_id' => 'integer',
        ]);

        $comment = Comment::create($validated);

        $this->redisCacheService->delete(config('redis_keys.comments'));

        return response()->json($comment, 201);
    }

    public function show(Comment $comment): JsonResponse
    {
        return response()->json($comment);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        $validated = $request->validate([
            'post_id'   => 'integer',
            'user_id'   => 'integer',
            'content'   => 'required|string',
            'status'    => 'integer',
            'parent_id' => 'integer',
        ]);

        $comment->update($validated);

        $this->redisCacheService->delete(config('redis_keys.comments'));

        return response()->json($comment);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        $this->redisCacheService->delete(config('redis_keys.comments'));

        return response()->json(null, 204);
    }
}

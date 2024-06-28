<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Comment::all());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'post_id' => 'required|integer',
            'user_id' => 'required|integer',
            'content' => 'required|string',
            'status' => 'integer',
            'parent_id' => 'integer',
        ]);

        $comment = Comment::create($validated);

        return response()->json($comment, 201);
    }

    public function show(Comment $comment): JsonResponse
    {
        return response()->json($comment);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        $validated = $request->validate([
            'post_id' => 'integer',
            'user_id' => 'integer',
            'content' => 'required|string',
            'status' => 'integer',
            'parent_id' => 'integer',
        ]);

        $comment->update($validated);

        return response()->json($comment);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json(['message' => 'Success removed'], 204);
    }
}

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

    /**
     * @OA\Get(
     *     path="/api/v1/comments",
     *     summary="Get list of comments",
     *     tags={"Comments"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", description="ID of the comment"),
     *                 @OA\Property(property="post_id", type="integer", description="ID of the post"),
     *                 @OA\Property(property="user_id", type="integer", description="ID of the user"),
     *                 @OA\Property(property="content", type="string", description="Content of the comment"),
     *                 @OA\Property(property="status", type="integer", description="Status of the comment"),
     *                 @OA\Property(property="parent_id", type="integer", description="ID of the parent comment"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/comments",
     *     summary="Create a new comment",
     *     tags={"Comments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"post_id", "user_id", "content"},
     *             @OA\Property(property="post_id", type="integer", description="ID of the post"),
     *             @OA\Property(property="user_id", type="integer", description="ID of the user"),
     *             @OA\Property(property="content", type="string", description="Content of the comment"),
     *             @OA\Property(property="status", type="integer", description="Status of the comment"),
     *             @OA\Property(property="parent_id", type="integer", description="ID of the parent comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comment created",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="ID of the comment"),
     *             @OA\Property(property="post_id", type="integer", description="ID of the post"),
     *             @OA\Property(property="user_id", type="integer", description="ID of the user"),
     *             @OA\Property(property="content", type="string", description="Content of the comment"),
     *             @OA\Property(property="status", type="integer", description="Status of the comment"),
     *             @OA\Property(property="parent_id", type="integer", description="ID of the parent comment"),
     *             @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/comments/{id}",
     *     summary="Get a comment by ID",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="ID of the comment"),
     *             @OA\Property(property="post_id", type="integer", description="ID of the post"),
     *             @OA\Property(property="user_id", type="integer", description="ID of the user"),
     *             @OA\Property(property="content", type="string", description="Content of the comment"),
     *             @OA\Property(property="status", type="integer", description="Status of the comment"),
     *             @OA\Property(property="parent_id", type="integer", description="ID of the parent comment"),
     *             @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found"
     *     )
     * )
     */
    public function show(Comment $comment): JsonResponse
    {
        return response()->json($comment);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/comments/{id}",
     *     summary="Update a comment",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="post_id", type="integer", description="ID of the post"),
     *             @OA\Property(property="user_id", type="integer", description="ID of the user"),
     *             @OA\Property(property="content", type="string", description="Content of the comment"),
     *             @OA\Property(property="status", type="integer", description="Status of the comment"),
     *             @OA\Property(property="parent_id", type="integer", description="ID of the parent comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="ID of the comment"),
     *             @OA\Property(property="post_id", type="integer", description="ID of the post"),
     *             @OA\Property(property="user_id", type="integer", description="ID of the user"),
     *             @OA\Property(property="content", type="string", description="Content of the comment"),
     *             @OA\Property(property="status", type="integer", description="Status of the comment"),
     *             @OA\Property(property="parent_id", type="integer", description="ID of the parent comment"),
     *             @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/v1/comments/{id}",
     *     summary="Delete a comment",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Comment deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found"
     *     )
     * )
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        $this->redisCacheService->delete(config('redis_keys.comments'));

        return response()->json(null, 204);
    }
}

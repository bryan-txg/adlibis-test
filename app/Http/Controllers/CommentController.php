<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with('user')
            ->whereNull('parent_id') // только корневые
            ->latest()
            ->cursorPaginate(5);

        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
            'commentable_type' => 'required|in:App\Models\Post,App\Models\News',
            'commentable_id' => 'required|integer',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Проверяем, что commentable существует
        $commentableClass = $validated['commentable_type'];
        $commentable = $commentableClass::findOrFail($validated['commentable_id']);

        // Если есть parent_id, проверяем что родитель относится к тому же контенту
        if (isset($validated['parent_id'])) {
            $parentComment = Comment::findOrFail($validated['parent_id']);
            
            if ($parentComment->commentable_type !== $validated['commentable_type'] ||
                $parentComment->commentable_id !== $validated['commentable_id']) {
                return response()->json([
                    'message' => 'Parent comment must belong to the same content'
                ], 422);
            }
        }

        $comment = Comment::create($validated);

        return response()->json($comment->load('user'), 201);
    }

    public function show(Comment $comment)
    {
        // Загружаем комментарий с его ответами (вложенные комментарии)
        $replies = $comment->replies()
            ->latest()
            ->cursorPaginate(20);

        return response()->json([
            'comment' => $comment->load('user'),
            'replies' => $replies,
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        // Проверяем, что пользователь - автор комментария
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        if ($comment->user_id != $validated['user_id']) {
            return response()->json([
                'message' => 'You can only edit your own comments'
            ], 403);
        }

        $comment->update(['content' => $validated['content']]);

        return response()->json($comment->load('user'));
    }

    public function destroy(Request $request, Comment $comment)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if ($comment->user_id != $request->user_id) {
            return response()->json([
                'message' => 'You can only delete your own comments'
            ], 403);
        }

        $comment->delete();

        return response()->json(null, 204);
    }
}

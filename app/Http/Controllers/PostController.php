<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->cursorPaginate(3);
        
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $post = Post::create($validated);

        return response()->json($post, 201);
    }

    public function show(Post $post)
    {
        $comments = $post->comments()
            ->whereNull('parent_id') // только корневые комментарии
            ->latest()
            ->cursorPaginate(20);

        return response()->json([
            'post' => $post,
            'comments' => $comments,
        ]);
    }
}

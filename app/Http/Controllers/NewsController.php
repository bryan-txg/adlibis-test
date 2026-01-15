<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->cursorPaginate(3);
        
        return response()->json($news);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $news = News::create($validated);

        return response()->json($news, 201);
    }

    public function show(News $news)
    {
        $comments = $news->comments()
            ->whereNull('parent_id') // только корневые комментарии
            ->latest()
            ->cursorPaginate(20);

        return response()->json([
            'news' => $news,
            'comments' => $comments,
        ]);
    }
}

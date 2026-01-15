<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;


Route::apiResource('posts', PostController::class)->only(['index', 'store', 'show']);

Route::apiResource('news', NewsController::class)->only(['index', 'store', 'show']);

Route::apiResource('comments', CommentController::class);

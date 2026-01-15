<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\News;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Создаём пользователей
        $users = User::factory(10)->create();

        // Создаём видео посты
        $posts = Post::factory(5)->create();

        // Создаём новости
        $news = News::factory(5)->create();

        // Создаём комментарии к постам
        $posts->each(function ($post) use ($users) {
            // Корневые комментарии к посту (parent_id = NULL)
            $rootComments = Comment::factory(5)->create([
                'user_id' => $users->random()->id,
                'commentable_type' => Post::class,
                'commentable_id' => $post->id,
                'parent_id' => null,
            ]);

            // Ответы на комментарии (parent_id = ID комментария)
            $rootComments->each(function ($comment) use ($users, $post) {
                Comment::factory(rand(1, 3))->create([
                    'user_id' => $users->random()->id,
                    'commentable_type' => Post::class,
                    'commentable_id' => $post->id,
                    'parent_id' => $comment->id,
                ]);
            });
        });

        // Создаём комментарии к новостям
        $news->each(function ($newsItem) use ($users) {
            // Корневые комментарии к новости (parent_id = NULL)
            $rootComments = Comment::factory(5)->create([
                'user_id' => $users->random()->id,
                'commentable_type' => News::class,
                'commentable_id' => $newsItem->id,
                'parent_id' => null,
            ]);

            // Ответы на комментарии
            $rootComments->each(function ($comment) use ($users, $newsItem) {
                Comment::factory(rand(1, 3))->create([
                    'user_id' => $users->random()->id,
                    'commentable_type' => News::class,
                    'commentable_id' => $newsItem->id,
                    'parent_id' => $comment->id,
                ]);
            });
        });
    }
}

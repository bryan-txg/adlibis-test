<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'commentable_id',
        'commentable_type',
        'parent_id',
    ];

    protected $with = ['user'];

    // К чему относится комментарий (Post/News)
    public function commentable()
    {
        return $this->morphTo();
    }

    // Автор комментария
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Родительский комментарий
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // Ответы на этот комментарий
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // Проверка, является ли комментарий корневым
    public function isRoot()
    {
        return is_null($this->parent_id);
    }
}

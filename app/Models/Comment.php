<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['body', 'post_id', 'user_id', 'parent_id',];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Родительский комментарий
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // Ответы
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->with('user', 'replies');
    }
}

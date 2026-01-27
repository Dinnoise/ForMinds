<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Tag;

class Post extends Model
{
    use CrudTrait;
    use HasFactory;
    protected $fillable = ['title', 'body', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)
        ->whereNull('parent_id')
        ->with('user', 'replies');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    
    
    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }
}

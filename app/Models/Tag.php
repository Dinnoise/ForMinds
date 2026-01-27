<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Связь: тег принадлежит многим постам
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}

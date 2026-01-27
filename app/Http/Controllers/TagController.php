<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    public function show(Tag $tag)
{
    $posts = $tag->posts()
        ->with('user')
        ->latest()
        ->paginate(10);

    return view('tags.show', compact('tag', 'posts'));
}
}

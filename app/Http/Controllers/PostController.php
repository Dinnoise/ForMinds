<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePostRequest;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::with('user', 'tags');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $posts = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('posts.index', compact('posts', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('posts.create', [
        'tags' => Tag::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
{
    // создаём пост без тегов
    $post = auth()->user()->posts()->create([
        'title' => $request->title,
        'body' => $request->body,
    ]);

    // прикрепляем теги, если они выбраны
    if ($request->has('tags')) {
        $post->tags()->sync($request->tags);
    }

    /* редирект на страницу  поста */
    return redirect()->route('posts.show', $post)
        ->with('success', 'Пост успешно создан.');
}


    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['user', 'comments.user', 'tags', 'likes'])
            ->loadCount('likes');

        return view('posts.show', compact('post'));
    }

    public function toggleLike(Post $post)
    {
        $user = auth()->user();

        $existingLike = $post->likes()
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
        } else {
            $post->likes()->create([
                'user_id' => $user->id,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
{
    if ($post->user_id !== Auth::id()) {
        abort(403);
    }

    $post->delete();

    return redirect()->route('profile.me');
}
}

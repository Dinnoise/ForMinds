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
        $query = Post::published()->with('user', 'tags');

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
        'title' => $request->title ?? '',
        'body' => $request->body ?? '',
        'is_draft' => $request->isDraft(),
    ]);

    // прикрепляем теги, если они выбраны
    if ($request->has('tags')) {
        $post->tags()->sync($request->tags);
    }

    if ($post->is_draft) {
        return redirect()->route('profile.me.drafts')
            ->with('success', 'Черновик сохранён.');
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
        // черновик видит только автор
        if ($post->is_draft && $post->user_id !== Auth::id()) {
            abort(404);
        }

        $post->load(['user', 'comments.user', 'tags', 'likes'])
            ->loadCount('likes');

        return view('posts.show', compact('post'));
    }

    public function toggleLike(Post $post)
    {
        abort_if($post->is_draft, 404);

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

        // AJAX-клик: страница не перезагружается, кнопку обновляет JS
        if (request()->expectsJson()) {
            return response()->json([
                'liked' => ! $existingLike,
                'count' => $post->likes()->count(),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Редактирование черновика
     */
    public function edit(Post $post)
    {
        $this->authorizeDraft($post);

        return view('posts.edit', [
            'post' => $post->load('tags'),
            'tags' => Tag::all(),
        ]);
    }

    /**
     * Сохранение черновика: остаётся черновиком или публикуется
     */
    public function update(StorePostRequest $request, Post $post)
    {
        $this->authorizeDraft($post);

        $post->fill([
            'title' => $request->title ?? '',
            'body' => $request->body ?? '',
        ]);

        if (! $request->isDraft()) {
            $this->markPublished($post);
        }

        $post->save();
        $post->tags()->sync($request->tags ?? []);

        if ($post->is_draft) {
            return redirect()->route('profile.me.drafts')
                ->with('success', 'Черновик сохранён.');
        }

        return redirect()->route('posts.show', $post)
            ->with('success', 'Пост опубликован.');
    }

    /**
     * Публикация черновика из списка черновиков
     */
    public function publish(Post $post)
    {
        $this->authorizeDraft($post);

        // недописанный черновик отправляем дописывать
        if (blank($post->title) || blank($post->body)) {
            return redirect()->route('posts.edit', $post)
                ->withErrors(['title' => 'Перед публикацией заполните заголовок и текст.']);
        }

        $this->markPublished($post);
        $post->save();

        return redirect()->route('posts.show', $post)
            ->with('success', 'Пост опубликован.');
    }

    private function authorizeDraft(Post $post): void
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        // опубликованные посты пока не редактируются
        abort_unless($post->is_draft, 404);
    }

    private function markPublished(Post $post): void
    {
        $post->is_draft = false;
        // дата поста — момент публикации, чтобы он попал наверх ленты
        $post->created_at = now();
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

    return redirect()->route($post->is_draft ? 'profile.me.drafts' : 'profile.me');
}
}

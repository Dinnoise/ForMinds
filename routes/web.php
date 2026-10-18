<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [PostController::class, 'index'])->name('home');

Route::get('/home', function () {
    return view('home');})
    ->middleware(['auth', 'verified'])
    ->name('home');

Route::get('/tags/{tag:slug}', [TagController::class, 'show'])
    ->name('tags.show');

Route::get('/posts', [PostController::class, 'index'])
    ->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile/posts', [ProfileController::class, 'myProfile'])
        ->name('profile.me');

    Route::get('/profile/comments', [ProfileController::class, 'myComments'])
        ->name('profile.me.comments');

    Route::get('/profile/likes', [ProfileController::class, 'myLikes'])
        ->name('profile.me.likes');

    Route::get('/profile/subscriptions', [ProfileController::class, 'mySubscriptions'])
        ->name('profile.me.subscriptions');

    Route::get('/profile/drafts', [ProfileController::class, 'myDrafts'])
        ->name('profile.me.drafts');

    Route::redirect('/profile', '/profile/posts');

    Route::patch('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->name('profile.destroy');
    
    Route::get('/posts', [PostController::class, 'index']);
    
    Route::get('/posts/create', [PostController::class, 'create'])
    ->middleware('auth')
    ->name('posts.create');
    
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
    ->name('comments.store');

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
    ->name('comments.destroy');

    // не /comments/...: в app.css есть стили по form[action*="comments"] для форм ответа
    Route::post('/reactions/{comment}', [CommentController::class, 'toggleReaction'])
    ->name('comments.react');

    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])
        ->name('posts.toggle-like');

    Route::post('/posts/{post}/publish', [PostController::class, 'publish'])
        ->name('posts.publish');

    Route::post('/users/{user}/subscribe', [ProfileController::class, 'toggleSubscription'])
        ->name('profile.toggle-subscription');


});

// Профиль другого пользователя
Route::get('/users/{user}', [ProfileController::class, 'show'])
    ->name('profile.show');

// Route::post('/users/{user}', [ProfileController::class, 'posts'])
//     ->name('profile.posts');

Route::get('/users/{user}/comments', [ProfileController::class, 'comments'])
    ->name('profile.comments');

Route::get('/users/{user}/likes', [ProfileController::class, 'likes'])
    ->name('profile.likes');

Route::resource('posts', PostController::class)
    ->middleware('auth');

require __DIR__.'/auth.php';

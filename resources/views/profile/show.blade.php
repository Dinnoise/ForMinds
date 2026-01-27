@extends('profile.layout')

@section('profile-content')

<h1>Профиль пользователя</h1>

<p><strong>Имя:</strong> {{ $user->username }}</p>

<hr>

<h2>Посты пользователя</h2>

@if($posts->isEmpty())
    <p>Постов пока нет</p>
@endif

@foreach($posts as $post)
    <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
        <h3>{{ $post->title }}</h3>
        <p>{{ $post->body }}</p>

        <small>
            {{ $post->created_at->format('d.m.Y H:i') }} |
            ❤️ {{ $post->likes_count }} |
            💬 {{ $post->allComments->count() }}
        </small>
    </div>
@endforeach
@endsection

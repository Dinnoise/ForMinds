@extends('profile.layout')

@section('profile-content')
<h1>Мой профиль</h1>

<p><strong>Имя:</strong> {{ $user->username }}</p>
<p><strong>Email:</strong> {{ $user->email }}</p>

<hr>

<h2>Мои посты</h2>

@if($posts->isEmpty())
    <p>Вы ещё не создали ни одного поста</p>
@endif

@foreach($posts as $post)
    <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
        <h3><a href="{{ route('posts.show', $post) }}">
                    {{ $post->title }}
                </a></h3>
        <p>{{ $post->body }}</p>

        <small>
            {{ $post->created_at->format('d.m.Y H:i') }} |
            ❤️ {{ $post->likes_count }} |
            💬 {{ $post->allComments->count() }}
        </small>

        <form method="POST" action="{{ route('posts.destroy', $post) }}">
            @csrf
            @method('DELETE')
            <button type="submit">Удалить пост</button>
        </form>
    </div>
@endforeach
@endsection

@extends('layouts.app')

@section('content')
    @foreach ($posts as $post)
        <article class="post">
            <h2>
                <a href="{{ route('posts.show', $post) }}">
                    {{ $post->title }}
                </a>
            </h2>

            <p>
                <a href="{{ route('profile.show', $post->user) }}">
                    {{ $post->user->username }}
                </a>
                • {{ $post->created_at->diffForHumans() }}
            </p>

            <p>{{ Str::limit($post->body, 200) }}</p>
        </article>
    @endforeach

    {{ $posts->links() }}
@endsection

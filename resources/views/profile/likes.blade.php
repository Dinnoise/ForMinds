@extends('profile.layout')

@section('profile-content')
    @forelse ($likes as $like)
        <div class="card mb-3">
            <div class="card-body">
                <small class="text-muted">

                <a href="{{ route('posts.show', $like->post) }}">
                    {{ $like->post->title }}
                </a>
                ❤️</small>

                <small class="text-muted">
                    <h4>Автор: {{ $like->post->user->username }}</h4>
                </small>
            </div>
        </div>
    @empty
        <p class="text-muted">Лайков нет</p>
    @endforelse
@endsection

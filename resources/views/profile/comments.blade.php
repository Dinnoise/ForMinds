@extends('profile.layout')

@section('profile-content')
    @forelse ($comments as $comment)
        <div class="card mb-3">
            <div class="card-body">
                <small class="text-muted">
                    Комментарий к посту
                    <a href="{{ route('posts.show', $comment->post) }}">
                        {{ $comment->post->title }}
                    </a>
                </small>

                <p class="mt-2">{{ $comment->body }}</p>

                <small class="text-muted">
                    {{ $comment->created_at->diffForHumans() }}
                </small>
            </div>
        </div>
    @empty
        <p class="text-muted">Комментариев нет</p>
    @endforelse
@endsection

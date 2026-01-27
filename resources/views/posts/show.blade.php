@extends('layouts.app')

@section('content')
<div class="container posts-show">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- ПОСТ --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>{{ $post->title }}</h2>
                    <span class="text-muted">
                        {{ $post->created_at->diffForHumans() }}
                    </span>
                </div>

                <div class="card-body">
                    <p>{{ $post->body }}</p>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group">
                            @php
                                $user = auth()->user();
                                $liked = $user ? $post->likes->contains('user_id', $user->id) : false;
                            @endphp

                            <form method="POST" action="{{ route('posts.toggle-like', $post) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $liked ? 'btn-primary' : 'btn-outline-primary' }}">
                                    {{ $liked ? 'Убрать лайк' : 'Лайк' }} ({{ $post->likes_count }})
                                </button>
                            </form>
                        </div>

                        <small class="text-muted">
                            Автор: <a href="{{ route('profile.show', $post->user) }}">
                            {{ $post->user->username }}
                        </a>
                        </small>
                    </div>
                </div>
            </div>

            {{-- ФОРМА КОММЕНТАРИЯ --}}
            @auth
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('comments.store', $post) }}">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}">

                        <div class="mb-3">
                            <textarea
                                name="body"
                                class="form-control"
                                rows="3"
                                placeholder="Написать комментарий..."
                                required
                            ></textarea>
                        </div>

                        <button class="btn btn-primary">
                            Отправить
                        </button>
                    </form>
                </div>
            </div>
            @endauth

            {{-- КОММЕНТАРИИ --}}
            <div class="card">
                <div class="card-header">
                    Комментарии ({{ $post->allComments->count() }})
                </div>

                <div class="card-body">
                    @forelse ($post->comments as $comment)
                        @include('comments.comment', [
                            'comment' => $comment,
                            'level' => 0
                        ])
                    @empty
                        <p class="text-muted">
                            Пока нет комментариев. Будьте первым!
                        </p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

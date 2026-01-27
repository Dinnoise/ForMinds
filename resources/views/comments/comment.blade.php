<div class="mb-3" style="margin-left: {{ $level * 20 }}px;">
    <div class="d-flex">
        <div class="flex-shrink-0"></div>

        <div class="ms-3 w-100">

            {{-- ШАПКА КОММЕНТАРИЯ --}}
            <div class="comment-header">
                <strong>{{ $comment->user->username }}</strong>
                <small class="text-muted">
                    {{ $comment->created_at->diffForHumans() }}
                </small>

                @auth
                    @if(auth()->id() === $comment->user_id)
                        <form method="POST"
                              action="{{ route('comments.destroy', $comment) }}"
                              onsubmit="return confirm('Удалить комментарий?')"
                              class="comment-delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Удалить</button>
                        </form>
                    @endif
                @endauth
            </div>

            {{-- ТЕКСТ КОММЕНТАРИЯ --}}
            <p class="mb-1">{{ $comment->body }}</p>

            {{-- ФОРМА ОТВЕТА --}}
            @auth
            <form method="POST" action="{{ route('comments.store', $post) }}">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                <textarea name="body" rows="2" required></textarea>
                <button class="btn btn-sm btn-outline-secondary">Ответить</button>
            </form>
            @endauth

            {{-- ОТВЕТЫ --}}
            @foreach ($comment->replies as $reply)
                @include('comments.comment', [
                    'comment' => $reply,
                    'level' => $level + 1
                ])
            @endforeach

        </div>
    </div>
</div>

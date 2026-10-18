<div class="comment-thread {{ $level > 0 ? 'comment-thread--reply' : '' }}">

    {{-- КАРТОЧКА КОММЕНТАРИЯ: всё, что относится к одному комментарию, внутри обводки --}}
    <div class="comment-box" id="comment-{{ $comment->id }}">

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
        <p class="comment-body">{{ $comment->body }}</p>

        {{-- РЕАКЦИИ: чипы со счётчиком + кнопка выбора стикера --}}
        @include('comments._reactions')

        {{-- ФОРМА ОТВЕТА --}}
        @auth
        <form method="POST" action="{{ route('comments.store', $post) }}" class="comment-reply">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">

            <textarea name="body" rows="2" placeholder="Ответить {{ $comment->user->username }}..." required></textarea>
            <button class="btn btn-sm btn-outline-secondary">Ответить</button>
        </form>
        @endauth
    </div>

    {{-- ОТВЕТЫ: под карточкой, со сдвигом и линией ветки --}}
    @if ($comment->replies->isNotEmpty())
        <div class="comment-replies">
            @foreach ($comment->replies as $reply)
                @include('comments.comment', [
                    'comment' => $reply,
                    'level' => $level + 1
                ])
            @endforeach
        </div>
    @endif

</div>

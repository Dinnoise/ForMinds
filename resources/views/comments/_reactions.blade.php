{{-- Блок реакций комментария. Рендерится и в дереве комментариев, и отдельно — в ответ на AJAX-клик по стикеру --}}
@php($reactions = $comment->reactionSummary(auth()->id()))

<div class="reactions" data-comment="{{ $comment->id }}">
    @auth
        <form method="POST" action="{{ route('comments.react', $comment) }}" class="reactions__form">
            @csrf

            @foreach ($reactions as $type => $reaction)
                <button type="submit" name="type" value="{{ $type }}"
                        class="reaction-chip {{ $reaction['mine'] ? 'is-mine' : '' }}"
                        title="{{ $reaction['mine'] ? 'Убрать реакцию' : 'Добавить такую же' }}">
                    <span class="reaction-chip__emoji">{{ $reaction['emoji'] }}</span>
                    <span>{{ $reaction['count'] }}</span>
                </button>
            @endforeach

            <div class="reaction-picker" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
                <button type="button" class="reaction-add" @click="open = ! open" :class="{ 'is-open': open }"
                        aria-label="Добавить реакцию" title="Добавить реакцию">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M8.5 14.5c.9 1.2 2.1 1.8 3.5 1.8s2.6-.6 3.5-1.8M9 9.5h.01M15 9.5h.01" />
                    </svg>
                    <span class="reaction-add__plus">+</span>
                </button>

                <div class="reaction-picker__menu" x-show="open" x-cloak x-transition.opacity.duration.150ms>
                    @foreach (\App\Models\Comment::REACTIONS as $type => $emoji)
                        <button type="submit" name="type" value="{{ $type }}"
                                class="reaction-picker__item {{ ($reactions[$type]['mine'] ?? false) ? 'is-mine' : '' }}">
                            {{ $emoji }}
                        </button>
                    @endforeach
                </div>
            </div>
        </form>
    @else
        @foreach ($reactions as $reaction)
            <span class="reaction-chip is-readonly">
                <span class="reaction-chip__emoji">{{ $reaction['emoji'] }}</span>
                <span>{{ $reaction['count'] }}</span>
            </span>
        @endforeach
    @endauth
</div>

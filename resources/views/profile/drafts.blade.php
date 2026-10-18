@extends('profile.layout')

@section('profile-content')
<h2>Черновики</h2>

@if (session('success'))
    <p class="flash-success">{{ session('success') }}</p>
@endif

@if($drafts->isEmpty())
    <p>Черновиков нет. Нажмите «В черновик» при создании поста, чтобы сохранить его и дописать позже.</p>
@endif

@foreach($drafts as $draft)
    <div class="draft-card">
        <h3>
            <a href="{{ route('posts.edit', $draft) }}">
                {{ filled($draft->title) ? $draft->title : 'Без названия' }}
            </a>
        </h3>

        @if (filled($draft->body))
            <p>{{ Str::limit($draft->body, 200) }}</p>
        @endif

        <small>
            Изменён {{ $draft->updated_at->format('d.m.Y H:i') }}
            @if ($draft->tags->isNotEmpty())
                · {{ $draft->tags->pluck('name')->join(', ') }}
            @endif
        </small>

        <div class="draft-card__actions">
            <a href="{{ route('posts.edit', $draft) }}" class="draft-card__btn">Редактировать</a>

            <form method="POST" action="{{ route('posts.publish', $draft) }}">
                @csrf
                <button type="submit" class="draft-card__btn draft-card__btn--primary">Опубликовать</button>
            </form>

            <form method="POST" action="{{ route('posts.destroy', $draft) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="draft-card__btn draft-card__btn--danger">Удалить</button>
            </form>
        </div>
    </div>
@endforeach
@endsection

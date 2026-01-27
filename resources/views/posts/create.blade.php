@extends('layouts.app')

@section('content')
<h1>Создать пост</h1>

<form method="POST" action="{{ route('posts.store') }}">
    @csrf

    <div>
        <label>Заголовок</label>
        <input type="text" name="title" required>
    </div>

    <div>
        <label>Теги</label>
        @foreach($tags as $tag)
            <label>
                <input type="checkbox" name="tags[]" value="{{ $tag->id }}">
                {{ $tag->name }}
            </label>
        @endforeach
    </div>

    <div>
        <label>Текст поста</label>
        <textarea name="body" rows="8" required></textarea>
    </div>

    <button type="submit">Опубликовать</button>
</form>
@endsection



{{-- Общая форма создания поста и редактирования черновика. $post — null при создании --}}
@php($selectedTags = old('tags', $post?->tags->pluck('id')->all() ?? []))

@if ($errors->any())
    <div class="form-errors">
        @foreach ($errors->unique() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div>
    <label>Заголовок</label>
    <input type="text" name="title" value="{{ old('title', $post?->title) }}" required>
</div>

<div class="form-tags">
    <label>Теги</label>
    @foreach($tags as $tag)
        <label>
            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array($tag->id, $selectedTags))>
            {{ $tag->name }}
        </label>
    @endforeach
</div>

<div>
    <label>Текст поста</label>
    <textarea name="body" rows="8" required>{{ old('body', $post?->body) }}</textarea>
</div>

<div class="form-actions">
    <button type="submit" name="action" value="publish">Опубликовать</button>
    {{-- formnovalidate: черновик можно сохранить, не заполнив все поля --}}
    <button type="submit" name="action" value="draft" class="btn-draft" formnovalidate>
        {{ $post ? 'Сохранить черновик' : 'В черновик' }}
    </button>
</div>

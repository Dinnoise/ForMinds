@extends('layouts.app')

@section('content')
<h1>Создать пост</h1>

<form method="POST" action="{{ route('posts.store') }}">
    @csrf

    @include('posts._form', ['post' => null])
</form>
@endsection

@extends('layouts.app')

@section('content')
<h1>Редактировать черновик</h1>

<form method="POST" action="{{ route('posts.update', $post) }}">
    @csrf
    @method('PUT')

    @include('posts._form')
</form>
@endsection

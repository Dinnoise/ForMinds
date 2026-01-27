@extends('profile.layout')

@section('profile-content')
    @forelse ($subscriptions as $followed)
        <div class="card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('profile.show', $followed) }}">
                        {{ $followed->username }}
                    </a>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">Подписок нет</p>
    @endforelse
@endsection

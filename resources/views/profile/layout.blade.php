@extends('layouts.app')

@section('content')
<div class="container col-md-8">

    @php
        $isOwner = auth()->check() && auth()->id() === $user->id;
        $isSubscribed = !$isOwner && auth()->check() && auth()->user()->following->contains($user->id);
    @endphp


    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>{{ $user->username }}</h3>

        @auth
            @if (! $isOwner)
                <form method="POST" action="{{ route('profile.toggle-subscription', $user) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $isSubscribed ? 'btn-outline-secondary' : 'btn-primary' }}">
                        {{ $isSubscribed ? 'Отписаться' : 'Подписаться' }}
                    </button>
                </form>
            @endif
        @endauth
    </div>

    <div class="mb-3">
        @if ($isOwner)
            <a href="{{ route('profile.me') }}"
               class="btn btn-sm {{ request()->routeIs('profile.me') ? 'btn-primary' : 'btn-outline-primary' }}">
                Посты
            </a>

            <a href="{{ route('profile.me.comments') }}"
               class="btn btn-sm {{ request()->routeIs('profile.me.comments') ? 'btn-primary' : 'btn-outline-primary' }}">
                Комментарии
            </a>

            <a href="{{ route('profile.me.likes') }}"
               class="btn btn-sm {{ request()->routeIs('profile.me.likes') ? 'btn-primary' : 'btn-outline-primary' }}">
                Лайки
            </a>
            <a href="{{ route('profile.me.subscriptions') }}"
               class="btn btn-sm {{ request()->routeIs('profile.me.subscriptions') ? 'btn-primary' : 'btn-outline-primary' }}">
                Подписки
            </a>
            <a href="{{ route('profile.me.drafts') }}"
               class="btn btn-sm {{ request()->routeIs('profile.me.drafts') ? 'btn-primary' : 'btn-outline-primary' }}">
                Черновики
            </a>
        @else
            <a href="{{ route('profile.show', $user) }}"
               class="btn btn-sm {{ request()->routeIs('profile.show') ? 'btn-primary' : 'btn-outline-primary' }}">
                Посты
            </a>

            <a href="{{ route('profile.comments', $user) }}"
               class="btn btn-sm {{ request()->routeIs('profile.comments') ? 'btn-primary' : 'btn-outline-primary' }}">
                Комментарии
            </a>

            <a href="{{ route('profile.likes', $user) }}"
               class="btn btn-sm {{ request()->routeIs('profile.likes') ? 'btn-primary' : 'btn-outline-primary' }}">
                Лайки
            </a>
        @endif
    </div>

    @yield('profile-content')

</div>
@endsection
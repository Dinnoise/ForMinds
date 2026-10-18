@php($currentTag = request()->route('tag'))

<nav x-data="{ menu: false, topics: {{ $currentTag ? 'true' : 'false' }} }"
     x-effect="document.body.style.overflow = menu ? 'hidden' : ''"
     @keydown.escape.window="menu = false"
     class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Бургер: открывает боковое меню -->
                <button type="button" class="burger-btn" :class="{ 'is-open': menu }"
                        @click="menu = ! menu" :aria-expanded="menu" aria-controls="side-menu" aria-label="Меню">
                    <span></span><span></span><span></span>
                </button>

                <!-- Logo -->
                <div class="header-logo">
                    <a href="{{ route('home') }}">Talkspace</a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <input
                            id="nav-search-input"
                            type="text"
                            placeholder="Поиск"
                            class="border rounded px-3 py-1"
                            value="{{ request('q') }}"
                        >
                    </div>

                    <x-nav-link :href="route('posts.create')">
                        Создать
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
            <!-- Переключатель темы: в светлой показывается луна, в тёмной — солнце -->
            <button type="button" class="theme-toggle" onclick="toggleTheme()" aria-label="Переключить тему" title="Переключить тему">
                <svg class="theme-toggle__moon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z" />
                </svg>
                <svg class="theme-toggle__sun" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="4" />
                    <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
                </svg>
            </button>

            @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->username }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.me')">
                            Профиль
                        </x-dropdown-link>

                        @php($draftsCount = Auth::user()->posts()->drafts()->count())
                        <x-dropdown-link :href="route('profile.me.drafts')">
                            Черновики
                            @if ($draftsCount)
                                <span class="dropdown-count">{{ $draftsCount }}</span>
                            @endif
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                                >                  
                                {{ __('Выйти') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="btn">
                    {{ __('Войти') }}
                </a>

                <a href="{{ route('register') }}" class="btn ms-2">
                    {{ __('Зарегистрироваться') }}
                </a>
            @endguest
        </div>
        </div>
    </div>

    <!-- Боковое меню. Видимость через класс, а не x-show: в app.css есть общее правило для nav [x-show] (dropdown профиля) -->
    <div class="side-menu-overlay" :class="{ 'is-open': menu }" @click="menu = false"></div>

    <aside id="side-menu" class="side-menu" :class="{ 'is-open': menu }" :aria-hidden="! menu">
        <div class="side-menu__head">
            <span class="side-menu__title">Меню</span>
            <button type="button" class="side-menu__close" @click="menu = false" aria-label="Закрыть меню">&times;</button>
        </div>

        <ul class="side-menu__list">
            <li>
                <a href="{{ route('home') }}" class="side-menu__item {{ request()->routeIs('home') ? 'is-active' : '' }}">
                    Главная
                </a>
            </li>

            <li>
                <button type="button" class="side-menu__item side-menu__toggle" :class="{ 'is-open': topics }"
                        @click="topics = ! topics" :aria-expanded="topics">
                    <span>Темы</span>
                    <svg class="side-menu__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 4.293a1 1 0 011.414 0l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414-1.414L11.586 10 7.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <ul class="side-menu__sub" :class="{ 'is-open': topics }">
                    @forelse ($tags as $tag)
                        <li>
                            <a href="{{ route('tags.show', $tag) }}"
                               class="side-menu__subitem {{ $currentTag?->is($tag) ? 'is-active' : '' }}">
                                <span>{{ $tag->name }}</span>
                                <span class="side-menu__count">{{ $tag->posts_count }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="side-menu__empty">Тем пока нет</li>
                    @endforelse
                </ul>
            </li>

            <li>
                <a href="{{ route('posts.create') }}" class="side-menu__item">
                    Создать тему
                </a>
            </li>
        </ul>
    </aside>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('nav-search-input');
    if (!input) return;

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const term = this.value.trim();
            const baseUrl = "{{ route('home') }}";
            
            if (!term) {
                window.location.href = baseUrl;
            } else {
                window.location.href = baseUrl + '?q=' + encodeURIComponent(term);
            }
        }
    });
});
</script>

@props(['links' => []])

@php $user = auth()->user(); @endphp

<header class="relative border-b-2 border-[var(--navy)] bg-white" x-data="navbar"
    @keydown.escape.window="closeAll()">
    <div class="container-page flex items-center justify-between gap-4 py-4">

        <a href="{{ auth()->check() ? route(auth()->user()->homeRoute()) : route('home') }}" class="brand">
            <span class="inline-block h-6 w-6 rounded-full bg-[var(--sun)]"></span>
            NAPC Connect
        </a>

        <nav class="hidden items-center gap-7 md:flex" aria-label="Main">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"
                    class="nav-link {{ !empty($link['active']) ? 'nav-link-active' : '' }}">{{ $link['label'] }}</a>
            @endforeach
        </nav>

        {{-- Desktop: account area --}}
        <div class="hidden items-center gap-3 md:flex">
            @auth
                <div class="relative" @click.outside="closeUser()">
                    <button type="button" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-[var(--mist)]"
                        @click="toggleUser()" :aria-expanded="userOpen" aria-haspopup="menu">
                        <span class="avatar">{{ mb_strtoupper(mb_substr($user->first_name, 0, 1)) }}</span>
                        <span class="text-[15px] font-bold text-[var(--navy)]">{{ $user->first_name }}</span>
                        <svg viewBox="0 0 20 20" class="h-4 w-4 text-[var(--navy)]" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 8l5 5 5-5" />
                        </svg>
                    </button>

                    <div x-show="userOpen" x-cloak class="nav-panel" role="menu">
                        <div class="px-3 py-2">
                            <p class="font-extrabold text-[var(--navy)]">{{ $user->name }}</p>
                            <p class="muted truncate">{{ $user->email }}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="badge-role">{{ $user->roleLabel() }}</span>
                                @if ($user->role === 'poster' && $user->poster_status)
                                    <span
                                        class="badge-{{ $user->poster_status }}">{{ ucfirst($user->poster_status) }}</span>
                                @endif
                            </div>
                        </div>
                        <hr class="my-1 border-[var(--mist)]">
                        <a href="{{ route($user->homeRoute()) }}" class="menu-item" role="menuitem">My home</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="menu-item" role="menuitem">Log out</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-ghost px-3 py-2 text-[15px]">Log in</a>
                <a href="{{ route('register') }}" class="btn-primary px-5 py-2.5 text-[15px]">Sign up</a>
            @endauth
        </div>

        {{-- Mobile: menu button --}}
        <button type="button" class="rounded-lg bg-[var(--mist)] px-4 py-2 font-bold md:hidden" @click="toggleMenu()"
            :aria-expanded="menuOpen" aria-controls="mobile-menu">
            <span x-text="menuLabel">Menu</span>
        </button>
    </div>

    {{-- Mobile panel --}}
    <div id="mobile-menu" x-show="menuOpen" x-cloak
        class="container-page border-t-2 border-[var(--mist)] pb-5 md:hidden">
        @auth
            <div class="flex items-center gap-3 py-4">
                <span class="avatar">{{ mb_strtoupper(mb_substr($user->first_name, 0, 1)) }}</span>
                <div class="min-w-0">
                    <p class="truncate font-extrabold text-[var(--navy)]">{{ $user->name }}</p>
                    <div class="mt-1 flex flex-wrap gap-2">
                        <span class="badge-role">{{ $user->roleLabel() }}</span>
                        @if ($user->role === 'poster' && $user->poster_status)
                            <span class="badge-{{ $user->poster_status }}">{{ ucfirst($user->poster_status) }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <hr class="border-[var(--mist)]">
        @endauth

        <div class="flex flex-col gap-1 pt-3 text-lg font-semibold">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"
                    class="py-2 {{ !empty($link['active']) ? 'font-extrabold text-[var(--navy)]' : '' }}">{{ $link['label'] }}</a>
            @endforeach

            @auth
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn-primary w-full">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="py-2">Log in</a>
                <a href="{{ route('register') }}" class="btn-primary mt-2">Sign up</a>
            @endauth
        </div>
    </div>
</header>

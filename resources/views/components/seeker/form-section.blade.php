@props(['number', 'title', 'hint' => null])

<section class="card-lg space-y-6 border-2 border-[var(--navy)]">
    <header>
        <h2 class="h3">{{ $number }}. {{ $title }}</h2>
        @if ($hint)
            <p class="muted mt-1">{{ $hint }}</p>
        @endif
    </header>

    {{ $slot }}
</section>

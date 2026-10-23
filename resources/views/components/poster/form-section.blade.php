@props(['number', 'title', 'hint' => null])

<section {{ $attributes->merge(['class' => 'card-lg border-2 border-[var(--navy)]']) }}>
    <div class="flex items-start gap-4">
        <span class="step-dot shrink-0">{{ $number }}</span>
        <div class="min-w-0">
            <h2 class="h3">{{ $title }}</h2>
            @if ($hint)
                <p class="muted mt-1">{{ $hint }}</p>
            @endif
        </div>
    </div>

    <div class="mt-6 space-y-6">
        {{ $slot }}
    </div>
</section>

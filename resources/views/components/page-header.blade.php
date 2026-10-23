@props(['title', 'subtitle' => null])

<section class="section-mist border-b-2 border-[var(--navy)]">
    <div class="container-page flex flex-col gap-4 py-8 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <h1 class="h2">{{ $title }}</h1>
            @if ($subtitle)
                <p class="lead mt-2">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex flex-wrap gap-3">{{ $actions }}</div>
        @endisset
    </div>
</section>

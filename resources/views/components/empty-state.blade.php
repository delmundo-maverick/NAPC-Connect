@props(['title'])

<div class="card-lg border-2 border-dashed border-[var(--navy)]/30 text-center">
    <p class="h3">{{ $title }}</p>
    @if (trim($slot))
        <p class="lead mx-auto mt-2 text-base">{{ $slot }}</p>
    @endif
</div>

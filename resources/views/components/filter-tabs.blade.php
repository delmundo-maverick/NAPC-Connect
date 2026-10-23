{{-- Filter chips that keep the rest of the query string (like the search text) --}}
@props(['options', 'current', 'param' => 'status'])

<nav class="flex flex-wrap gap-2" aria-label="Filter">
    @foreach ($options as $value => $label)
        <a href="{{ request()->fullUrlWithQuery([$param => $value, 'page' => null]) }}"
           class="{{ $current === $value ? 'chip-active' : 'chip' }}"
           @if ($current === $value) aria-current="true" @endif>{{ $label }}</a>
    @endforeach
</nav>

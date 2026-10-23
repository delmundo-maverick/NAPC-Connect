{{-- GET search form. "keep" lists other query parameters (like status) to carry along. --}}
@props(['placeholder' => 'Search', 'keep' => []])

<form method="GET" action="{{ url()->current() }}" role="search"
      class="flex w-full items-center gap-2 lg:max-w-lg"
      x-data="searchBox" data-initial="{{ request('q') }}">

    @foreach ($keep as $name)
        @if (request()->filled($name))
            <input type="hidden" name="{{ $name }}" value="{{ request($name) }}">
        @endif
    @endforeach

    <label for="q" class="sr-only">{{ $placeholder }}</label>
    <input id="q" type="search" name="q" x-model="value" autocomplete="off" class="input" placeholder="{{ $placeholder }}">

    <button type="button" x-show="hasValue" x-cloak @click="clear()" class="btn-ghost shrink-0 px-3 py-2 text-[15px]">Clear</button>
    <button type="submit" class="btn-primary shrink-0 px-5">Search</button>
</form>

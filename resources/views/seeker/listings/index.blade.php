@extends('layouts.app')
@section('title', 'Browse listings · NAPC Connect')

@section('navbar')
    <x-navbar.seeker />
@endsection

@php
    $options = ['all' => 'All (' . $counts['all'] . ')'];
    foreach (config('napc.types') as $key => $label) {
        $options[$key] = $label . ' (' . $counts[$key] . ')';
    }

    $hasFilters = $search !== '' || $region !== '' || $sort !== 'latest';
@endphp

@section('content')
    <x-page-header title="Browse listings" subtitle="Ayuda, scholarships, jobs and training you can apply for." />

    <div class="container-page space-y-6 py-10">

        <x-filter-tabs :options="$options" :current="$type" param="type" />

        <form method="GET" action="{{ route('seeker.listings.index') }}" class="filter-bar">
            @if ($type !== 'all')
                <input type="hidden" name="type" value="{{ $type }}">
            @endif

            <div>
                <label for="q" class="label">Search</label>
                <input id="q" type="search" name="q" value="{{ $search }}" class="input"
                    placeholder="Title or keyword">
            </div>

            <div>
                <label for="region" class="label">Location</label>
                <select id="region" name="region" class="input">
                    <option value="">Anywhere in the Philippines</option>
                    @foreach ($regions as $code => $name)
                        <option value="{{ $code }}" @selected($region === $code)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sort" class="label">Sort by</label>
                <select id="sort" name="sort" class="input">
                    <option value="latest" @selected($sort === 'latest')>Newest</option>
                    <option value="deadline" @selected($sort === 'deadline')>Deadline soonest</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Search</button>
                @if ($hasFilters)
                    <a href="{{ route('seeker.listings.index', $type !== 'all' ? ['type' => $type] : []) }}"
                        class="btn-ghost border-2 border-[var(--navy)]">Clear</a>
                @endif
            </div>
        </form>

        <p class="muted font-semibold">
            {{ $listings->total() }} {{ \Illuminate\Support\Str::plural('listing', $listings->total()) }}
            @if ($search !== '')
                for “{{ $search }}”
            @endif
            @if ($region !== '')
                in {{ $regions[$region] }} and nationwide
            @endif
        </p>

        <div class="grid gap-6 md:grid-cols-2">
            @forelse ($listings as $listing)
                <x-seeker.listing-card :listing="$listing" />
            @empty
                <div class="md:col-span-2">
                    <x-empty-state title="Nothing found">
                        @if ($hasFilters || $type !== 'all')
                            Try another category, a different keyword, or clear the filters.
                        @else
                            No listings are open right now. Please check back soon.
                        @endif
                    </x-empty-state>
                </div>
            @endforelse
        </div>

        {{ $listings->links('pagination.brand') }}
    </div>
@endsection

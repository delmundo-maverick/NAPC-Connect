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

        <form method="GET" action="{{ route('seeker.listings.index') }}" class="space-y-4">
            @if ($type !== 'all')
                <input type="hidden" name="type" value="{{ $type }}">
            @endif

            {{-- Search and sort --}}
            <div class="browse-filter">
                <div>
                    <label for="q" class="label">Search</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[var(--navy)]"
                            width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m20 20-3.5-3.5" />
                        </svg>
                        <input id="q" type="search" name="q" value="{{ $search }}"
                            class="input !bg-none !pl-12" placeholder="Title or keyword">
                    </div>
                </div>

                <div>
                    <label for="sort" class="label">Sort by</label>
                    <select id="sort" name="sort" class="input">
                        <option value="latest" @selected($sort === 'latest')>Newest</option>
                        <option value="deadline" @selected($sort === 'deadline')>Deadline soonest</option>
                    </select>
                </div>
            </div>

            {{-- Location --}}
            <x-location-picker :region="$region" :province="$province" :city="$city" :columns="3" region-label="Region"
                region-placeholder="Anywhere in the Philippines" province-placeholder="Any province"
                city-label="City or municipality" city-placeholder="Any city or municipality" />

            {{-- Buttons --}}
            <div class="filter-actions">
                @if ($hasFilters)
                    <a href="{{ route('seeker.listings.index', $type !== 'all' ? ['type' => $type] : []) }}"
                        class="btn-ghost border-2 border-[var(--navy)]">Clear</a>
                @endif
                <button type="submit" class="btn-primary">Search</button>
            </div>
        </form>

        {{-- Result count --}}
        <p class="muted font-semibold">
            {{ $listings->total() }} {{ \Illuminate\Support\Str::plural('listing', $listings->total()) }}
            @if ($search !== '')
                for “{{ $search }}”
            @endif
            @if ($area)
                for {{ $area }} (including nationwide and wider-area listings)
            @endif
        </p>

        {{-- Results --}}
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

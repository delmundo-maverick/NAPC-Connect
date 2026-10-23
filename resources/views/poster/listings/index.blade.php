@extends('layouts.app')
@section('title', 'My listings · NAPC Connect')

@section('navbar')
    <x-navbar.poster />
@endsection

@php
    $options = [
        'all' => 'All (' . $counts['all'] . ')',
        'published' => 'Published (' . $counts['published'] . ')',
        'draft' => 'Drafts (' . $counts['draft'] . ')',
        'closed' => 'Closed (' . $counts['closed'] . ')',
    ];
@endphp

@section('content')
    <x-page-header title="My listings" subtitle="Everything your organization has posted.">
        <x-slot:actions>
            <a href="{{ route('poster.listings.create') }}" class="btn-accent">Post a listing</a>
        </x-slot:actions>
    </x-page-header>

    <div class="container-page space-y-6 py-10">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <x-filter-tabs :options="$options" :current="$status" param="status" />
            <x-search-box placeholder="Search your listings by title" :keep="['status']" />
        </div>

        <p class="muted font-semibold">
            {{ $listings->total() }} {{ \Illuminate\Support\Str::plural('listing', $listings->total()) }}
            @if ($search !== '')
                for “{{ $search }}”
            @endif
        </p>

        <div class="space-y-6">
            @forelse ($listings as $listing)
                <x-poster.listing-card :listing="$listing" />
            @empty
                <x-empty-state title="No listings here yet">
                    @if ($counts['all'] === 0)
                        Post your first scholarship, ayuda, job or training program.
                    @else
                        Try another status or a different search.
                    @endif
                </x-empty-state>
            @endforelse
        </div>

        {{ $listings->links('pagination.brand') }}
    </div>
@endsection

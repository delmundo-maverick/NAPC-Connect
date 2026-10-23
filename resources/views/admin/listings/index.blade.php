@extends('layouts.app')
@section('title', 'Listings · NAPC Connect')

@section('navbar')
    <x-navbar.admin />
@endsection

@php
    $options = [
        'all' => 'All (' . $counts['all'] . ')',
        'published' => 'Live (' . $counts['published'] . ')',
        'closed' => 'Closed (' . $counts['closed'] . ')',
        'removed' => 'Taken down (' . $counts['removed'] . ')',
    ];
    $hasFilters = $search !== '' || $type !== 'all';
@endphp

@section('content')
    <x-page-header title="Listings" subtitle="Review what organizations posted and take down anything that doesn’t belong." />

    <div class="container-page space-y-6 py-10">

        <x-filter-tabs :options="$options" :current="$filter" param="status" />

        <form method="GET" action="{{ route('admin.listings.index') }}" class="filter-bar">
            @if ($filter !== 'all')
                <input type="hidden" name="status" value="{{ $filter }}">
            @endif

            <div>
                <label for="q" class="label">Search</label>
                <input id="q" type="search" name="q" value="{{ $search }}" class="input"
                    placeholder="Listing title or organization">
            </div>

            <div>
                <label for="type" class="label">Category</label>
                <select id="type" name="type" class="input">
                    <option value="all">All categories</option>
                    @foreach (config('napc.types') as $key => $label)
                        <option value="{{ $key }}" @selected($type === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 md:col-span-2">
                <button type="submit" class="btn-primary">Search</button>
                @if ($hasFilters)
                    <a href="{{ route('admin.listings.index', $filter !== 'all' ? ['status' => $filter] : []) }}"
                        class="btn-ghost border-2 border-[var(--navy)]">Clear</a>
                @endif
            </div>
        </form>

        <p class="muted font-semibold">
            {{ $listings->total() }} {{ \Illuminate\Support\Str::plural('listing', $listings->total()) }}
            @if ($search !== '')
                for “{{ $search }}”
            @endif
        </p>

        <div class="space-y-6">
            @forelse ($listings as $listing)
                @php
                    $removed = $listing->isTakenDown();
                    $statusBadge = $removed
                        ? 'badge-removed'
                        : ($listing->status === 'published'
                            ? 'badge-approved'
                            : 'badge-role');
                    $statusText = $removed ? 'Taken down' : ($listing->status === 'published' ? 'Live' : 'Closed');
                @endphp

                <article class="card-lg border-2 border-[var(--navy)]">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge-{{ $listing->type }}">{{ $listing->typeLabel() }}</span>
                        <span class="{{ $statusBadge }}">{{ $statusText }}</span>
                    </div>

                    <h3 class="h3 mt-3 break-words">{{ $listing->title }}</h3>

                    <dl class="detail-list mt-4">
                        <div>
                            <dt>Organization</dt>
                            <dd>{{ $listing->user?->organization ?: $listing->user?->name }}</dd>
                        </div>
                        <div>
                            <dt>Contact</dt>
                            <dd class="break-all">{{ $listing->user?->email }}</dd>
                        </div>
                        <div>
                            <dt>Available in</dt>
                            <dd>{{ $listing->locationLabel() }}</dd>
                        </div>
                        <div>
                            <dt>Deadline</dt>
                            <dd>{{ $listing->deadlineLabel() }}</dd>
                        </div>
                        @if ($listing->apply_mode === 'form')
                            <div>
                                <dt>Applications</dt>
                                <dd>{{ $listing->applications_count }}</dd>
                            </div>
                        @endif
                        @if ($listing->source_url)
                            <div>
                                <dt>Announcement</dt>
                                <dd><a href="{{ $listing->source_url }}" target="_blank" rel="noopener noreferrer"
                                        class="font-bold underline">Open link</a></dd>
                            </div>
                        @endif
                    </dl>

                    @if ($removed)
                        <x-alert type="danger">
                            <p class="font-extrabold">Taken down on {{ $listing->taken_down_at->format('M j, Y') }}</p>
                            <p class="mt-1 text-block">{{ $listing->takedown_reason }}</p>
                        </x-alert>
                    @endif

                    <details class="mod-details mt-5">
                        <summary>Read the full description</summary>
                        <p class="text-block mt-3">{{ $listing->description }}</p>
                    </details>

                    <div class="mt-6">
                        @if ($removed)
                            <form method="POST" action="{{ route('admin.listings.restore', $listing) }}"
                                x-data="confirmForm"
                                data-confirm="Restore “{{ $listing->title }}”? The organization will be able to reopen it."
                                @submit="submit($event)">
                                @csrf
                                <button type="submit" class="btn-primary btn-sm">Restore</button>
                            </form>
                        @else
                            <details class="mod-details">
                                <summary>Take down</summary>
                                <form method="POST" action="{{ route('admin.listings.takedown', $listing) }}"
                                    class="mt-3 space-y-3" x-data="confirmForm"
                                    data-confirm="Take down “{{ $listing->title }}”? Seekers will no longer see it."
                                    @submit="submit($event)">
                                    @csrf
                                    <label class="label" for="reason_{{ $listing->id }}">Reason (the organization
                                        will see this)</label>
                                    <textarea id="reason_{{ $listing->id }}" name="reason" rows="3" required minlength="5" maxlength="500"
                                        class="input" placeholder="e.g. We could not verify this program."></textarea>
                                    <button type="submit" class="btn-danger btn-sm">Take down listing</button>
                                </form>
                            </details>
                        @endif
                    </div>
                </article>
            @empty
                <x-empty-state title="No listings here">
                    @if ($hasFilters || $filter !== 'all')
                        Try another status or a different search.
                    @else
                        Listings appear here once organizations publish them.
                    @endif
                </x-empty-state>
            @endforelse
        </div>

        {{ $listings->links('pagination.brand') }}
    </div>
@endsection

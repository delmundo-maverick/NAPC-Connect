@extends('layouts.app')
@section('title', 'My applications · NAPC Connect')

@section('navbar')
    <x-navbar.seeker />
@endsection

@section('content')
    <x-page-header title="My applications" subtitle="Everything you applied for through NAPC Connect.">
        <x-slot:actions>
            <a href="{{ route('seeker.listings.index') }}" class="btn-accent">Browse listings</a>
        </x-slot:actions>
    </x-page-header>

    <div class="container-page space-y-6 py-10">

        <p class="muted font-semibold">
            {{ $applications->total() }}
            {{ \Illuminate\Support\Str::plural('application', $applications->total()) }}
        </p>

        <div class="space-y-6">
            @forelse ($applications as $application)
                @php $listing = $application->listing; @endphp

                <article class="card-lg border-2 border-[var(--navy)]">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge-{{ $listing->type }}">{{ $listing->typeLabel() }}</span>
                        <span class="badge-muted">{{ $application->statusLabel() }}</span>
                    </div>

                    <h3 class="h3 mt-3 break-words">
                        <a href="{{ route('seeker.listings.show', $listing) }}"
                            class="hover:underline">{{ $listing->title }}</a>
                    </h3>

                    @if ($listing->user?->organization)
                        <p class="muted mt-1 font-semibold">{{ $listing->user->organization }}</p>
                    @endif

                    <dl class="detail-list mt-4">
                        <div>
                            <dt>Applied on</dt>
                            <dd>{{ $application->created_at->format('M j, Y') }}</dd>
                        </div>
                        <div>
                            <dt>Listing</dt>
                            <dd>{{ $listing->status === 'published' && !$listing->isExpired() ? 'Still open' : 'Closed' }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-6">
                        <a href="{{ route('seeker.listings.show', $listing) }}"
                            class="btn-ghost btn-sm border-2 border-[var(--navy)]">View listing</a>
                    </div>
                </article>
            @empty
                <x-empty-state title="No applications yet">
                    Applications you send through an NAPC Connect form show up here.
                </x-empty-state>
            @endforelse
        </div>

        {{ $applications->links('pagination.brand') }}
    </div>
@endsection

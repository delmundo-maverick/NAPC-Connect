@extends('layouts.app')
@section('title', 'Organization dashboard · NAPC Connect')

@section('navbar')
    <x-navbar.poster />
@endsection

@php
    $orgTypes = [
        'school' => 'School or university',
        'lgu' => 'Local government unit (LGU)',
        'agency' => 'Government agency',
        'company' => 'Company or recruiter',
        'ngo' => 'NGO or non-profit',
    ];
    $approved = $user->poster_status === 'approved';
    $host = parse_url($user->proof_url, PHP_URL_HOST) ?: $user->proof_url;
@endphp

@section('content')
    <x-page-header title="Organization dashboard" :subtitle="$user->organization">
        <x-slot:actions>
            @if ($approved)
                <a href="{{ route('poster.listings.create') }}" class="btn-accent">Post a listing</a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="section space-y-10">

        {{-- Review status --}}
        @if ($user->poster_status === 'pending')
            <x-alert type="warning">Your organization is under review. You can post listings once an admin approves
                it.</x-alert>
        @elseif ($approved)
            <x-alert type="success">Your organization is verified. Your listings carry the Verified poster badge.</x-alert>
        @elseif ($user->poster_status === 'rejected')
            <x-alert type="danger">Your application was not approved. Check that your proof link is correct and contact the
                admin team.</x-alert>
        @endif

        {{-- Numbers --}}
        @if ($approved)
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <a href="{{ route('poster.listings.index', ['status' => 'published']) }}" class="stat-card stat-card-link">
                    <p class="stat-value">{{ $counts['published'] }}</p>
                    <p class="stat-label">Published</p>
                </a>
                <a href="{{ route('poster.listings.index', ['status' => 'draft']) }}" class="stat-card stat-card-link">
                    <p class="stat-value">{{ $counts['draft'] }}</p>
                    <p class="stat-label">Drafts</p>
                </a>
                <a href="{{ route('poster.listings.index', ['status' => 'closed']) }}" class="stat-card stat-card-link">
                    <p class="stat-value">{{ $counts['closed'] }}</p>
                    <p class="stat-label">Closed</p>
                </a>
                <div class="stat-card">
                    <p class="stat-value">{{ $counts['applications'] }}</p>
                    <p class="stat-label">Applications received</p>
                </div>
            </div>
        @endif

        {{-- Recent listings --}}
        <section>
            <div class="flex flex-wrap items-end justify-between gap-3">
                <h2 class="h2">Your listings</h2>
                @if ($approved && $recent->isNotEmpty())
                    <a href="{{ route('poster.listings.index') }}" class="font-bold text-[var(--sky)] underline">See all</a>
                @endif
            </div>

            <div class="mt-6 space-y-6">
                @forelse ($recent as $listing)
                    <x-poster.listing-card :listing="$listing" compact />
                @empty
                    <x-empty-state title="You haven’t posted anything yet">
                        @if ($approved)
                            Post a scholarship, ayuda, job or training program and we will show it to the people it fits.
                        @else
                            Once your organization is approved, you can post scholarships, ayuda, jobs and training
                            programs.
                        @endif
                    </x-empty-state>

                    @if ($approved)
                        <a href="{{ route('poster.listings.create') }}" class="btn-primary">Post your first listing</a>
                    @endif
                @endforelse
            </div>
        </section>

        {{-- Organization details --}}
        <section class="card-lg bg-[var(--mist)]">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="h3">Your organization</h2>
                <span class="badge-{{ $user->poster_status }}">{{ ucfirst($user->poster_status) }}</span>
            </div>

            <dl class="detail-list mt-5">
                <div>
                    <dt>Organization</dt>
                    <dd>{{ $user->organization }}</dd>
                </div>
                <div>
                    <dt>Type</dt>
                    <dd>{{ $orgTypes[$user->organization_type] ?? $user->organization_type }}</dd>
                </div>
                <div>
                    <dt>Your position</dt>
                    <dd>{{ $user->position }}</dd>
                </div>
                <div>
                    <dt>Proof link</dt>
                    <dd><a href="{{ $user->proof_url }}" target="_blank" rel="noopener noreferrer"
                            class="text-[var(--sky)] underline">{{ $host }}</a></dd>
                </div>
            </dl>
        </section>
    </div>
@endsection

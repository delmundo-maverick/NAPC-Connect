@extends('layouts.app')
@section('title', 'Admin · NAPC Connect')


@section('navbar')
    <x-navbar.admin />
@endsection
@section('content')
    <x-page-header title="Admin dashboard" subtitle="Review organizations before they can publish." />

    <div class="container-page space-y-12 py-10">

        {{-- Numbers (each one opens the matching list) --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <a href="{{ route('admin.users.index', ['role' => 'seeker']) }}" class="stat-card stat-card-link">
                <p class="stat-value">{{ $counts['seekers'] }}</p>
                <p class="stat-label">Seekers</p>
            </a>
            <a href="{{ route('admin.organizations.index', ['status' => 'approved']) }}" class="stat-card stat-card-link">
                <p class="stat-value">{{ $counts['approved'] }}</p>
                <p class="stat-label">Verified organizations</p>
            </a>
            <a href="{{ route('admin.organizations.index', ['status' => 'pending']) }}" class="stat-card stat-card-link">
                <p class="stat-value">{{ $counts['pending'] }}</p>
                <p class="stat-label">Waiting for review</p>
            </a>
            <a href="{{ route('admin.organizations.index', ['status' => 'rejected']) }}" class="stat-card stat-card-link">
                <p class="stat-value">{{ $counts['rejected'] }}</p>
                <p class="stat-label">Rejected</p>
            </a>
        </div>

        {{-- Review queue --}}
        <section>
            <div class="flex flex-wrap items-end justify-between gap-3">
                <h2 class="h2">Waiting for review</h2>
                @if ($counts['pending'] > $pending->count())
                    <a href="{{ route('admin.organizations.index', ['status' => 'pending']) }}"
                        class="font-bold text-[var(--sky)] underline">
                        See all {{ $counts['pending'] }}
                    </a>
                @endif
            </div>

            <div class="mt-6 space-y-6">
                @forelse ($pending as $org)
                    <x-org-card :org="$org" />
                @empty
                    <x-empty-state title="Nothing to review right now">New organization sign-ups will appear
                        here.</x-empty-state>
                @endforelse
            </div>
        </section>

        {{-- Newest accounts --}}
        <section>
            <div class="flex flex-wrap items-end justify-between gap-3">
                <h2 class="h2">Newest accounts</h2>
                <a href="{{ route('admin.users.index') }}" class="font-bold text-[var(--sky)] underline">All users</a>
            </div>

            <ul class="list-card mt-6">
                @foreach ($latest as $account)
                    <x-user-row :user="$account" />
                @endforeach
            </ul>
        </section>
    </div>
@endsection

@extends('layouts.app')
@section('title', 'Edit listing · NAPC Connect')

@section('navbar')
    <x-navbar.poster />
@endsection

@section('content')
    <x-page-header title="Edit listing" :subtitle="$listing->title">
        <x-slot:actions>
            @if ($listing->apply_mode === 'form')
                <a href="{{ route('poster.listings.applications', $listing) }}"
                    class="btn-ghost border-2 border-[var(--navy)]">Applicants</a>
            @endif
            <a href="{{ route('poster.listings.index') }}" class="btn-ghost border-2 border-[var(--navy)]">Back to
                listings</a>
        </x-slot:actions>
    </x-page-header>

    <div class="container-page py-10">
        <div class="mx-auto max-w-3xl">
            @if ($listing->isTakenDown())
                <div class="mb-8">
                    <x-alert type="danger">
                        <p class="font-extrabold">An admin took this listing down.</p>
                        <p class="mt-1 text-block">{{ $listing->takedown_reason }}</p>
                        <p class="mt-1">Fix what’s mentioned and save. It stays hidden until the admin restores it.</p>
                    </x-alert>
                </div>
            @endif
            @include('poster.listings._form', [
                'listing' => $listing,
                'action' => route('poster.listings.update', $listing),
                'method' => 'PUT',
            ])
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('title', 'Post a listing · NAPC Connect')

@section('navbar')
    <x-navbar.poster />
@endsection

@section('content')
    <x-page-header title="Post a listing" subtitle="Tell seekers who can apply and how. We match it to the people it fits.">
        <x-slot:actions>
            <a href="{{ route('poster.listings.index') }}" class="btn-ghost border-2 border-[var(--navy)]">Back to
                listings</a>
        </x-slot:actions>
    </x-page-header>

    <div class="container-page py-10">
        <div class="mx-auto max-w-3xl">
            @include('poster.listings._form', [
                'listing' => $listing,
                'action' => route('poster.listings.store'),
                'method' => 'POST',
            ])
        </div>
    </div>
@endsection

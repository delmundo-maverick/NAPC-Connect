@extends('layouts.app')
@section('title', 'My home · NAPC Connect')


@section('navbar')
    <x-navbar.seeker />
@endsection
@section('content')
    <x-page-header :title="'Kumusta, ' . auth()->user()->first_name" subtitle="Find ayuda, scholarships, jobs and training that fit you." />

    <div class="section space-y-12">

        {{-- Profile prompt --}}
        <div class="card-lg flex flex-col gap-5 bg-[var(--mist)] md:flex-row md:items-center md:justify-between">
            <div class="max-w-2xl">
                <h2 class="h3">{{ $completion >= 100 ? 'Your profile is complete' : 'Get suggestions made for you' }}
                </h2>
                <p class="lead mt-2 text-base">Add your year level, school, location and sector. We use them to show
                    programs you can actually apply for, with a short reason for each match.</p>
            </div>
            <a href="{{ route('seeker.profile.edit') }}" class="btn-primary shrink-0">
                @if ($completion === 0)
                    Set up profile
                @elseif ($completion >= 100)
                    Edit profile
                @else
                    Finish profile ({{ $completion }}%)
                @endif
            </a>
        </div>

        {{-- Recommendations --}}
        <section>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="h2">Recommended for you</h2>
                @if ($recommended->isNotEmpty())
                    <a href="{{ route('seeker.listings.index') }}" class="font-bold underline">Browse all listings</a>
                @endif
            </div>

            @if ($recommended->isNotEmpty())
                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    @foreach ($recommended as $listing)
                        <x-seeker.listing-card :listing="$listing" />
                    @endforeach
                </div>
            @else
                <div class="card-lg mt-6 border-2 border-dashed border-[var(--navy)]/30 text-center">
                    @if ($completion === 0)
                        <p class="h3">Your suggestions will show up here</p>
                        <p class="lead mx-auto mt-2 text-base">Set up your profile and the best matches appear first.
                        </p>
                    @else
                        <p class="h3">No matches yet</p>
                        <p class="lead mx-auto mt-2 text-base">Nothing open fits your profile right now. Check back
                            soon, or browse everything below.</p>
                    @endif
                </div>
            @endif
        </section>

        {{-- Browse --}}
        <section>
            <h2 class="h2">Browse by category</h2>
            <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
                <a href="{{ route('seeker.listings.index', ['type' => 'ayuda']) }}"
                    class="cat-card cat-ayuda !min-h-[120px]"><span class="text-xl font-extrabold">Ayuda</span></a>
                <a href="{{ route('seeker.listings.index', ['type' => 'scholarship']) }}"
                    class="cat-card cat-scholarship !min-h-[120px]"><span
                        class="text-xl font-extrabold">Scholarships</span></a>
                <a href="{{ route('seeker.listings.index', ['type' => 'job']) }}"
                    class="cat-card cat-job !min-h-[120px]"><span class="text-xl font-extrabold">Jobs</span></a>
                <a href="{{ route('seeker.listings.index', ['type' => 'training']) }}"
                    class="cat-card cat-training !min-h-[120px]"><span class="text-xl font-extrabold">Training</span></a>
            </div>
        </section>
    </div>
@endsection

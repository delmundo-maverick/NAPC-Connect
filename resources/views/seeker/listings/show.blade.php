@extends('layouts.app')
@section('title', $listing->title . ' · NAPC Connect')

@section('navbar')
    <x-navbar.seeker />
@endsection

@php
    $requirements = $listing->requirementRows();
    $details = $listing->detailRows();
@endphp

@section('content')
    <x-page-header :title="$listing->title" :subtitle="$listing->user?->organization">
        <x-slot:actions>
            <a href="{{ route('seeker.listings.index', ['type' => $listing->type]) }}"
                class="btn-ghost border-2 border-[var(--navy)]">Back to {{ strtolower($listing->typeLabel()) }} listings</a>
        </x-slot:actions>
    </x-page-header>

    <div class="container-page py-10">
        <div class="grid gap-6 lg:grid-cols-3">

            <div class="space-y-6 lg:col-span-2">
                <section class="card-lg space-y-4 border-2 border-[var(--navy)]">
                    <span class="badge-{{ $listing->type }} self-start">{{ $listing->typeLabel() }}</span>
                    <h2 class="h3">About this {{ strtolower($listing->typeLabel()) }}</h2>
                    <p class="text-block">{{ $listing->description }}</p>

                    @if ($listing->source_url)
                        <a href="{{ $listing->source_url }}" target="_blank" rel="noopener noreferrer"
                            class="font-bold underline">See the official announcement</a>
                    @endif
                </section>

                <section class="card-lg border-2 border-[var(--navy)]">
                    <h2 class="h3">Who can apply</h2>
                    <dl class="detail-list mt-4">
                        @foreach ($requirements as $label => $value)
                            <div class="{{ $label === 'Other requirements' ? 'sm:col-span-2' : '' }}">
                                <dt>{{ $label }}</dt>
                                <dd class="text-block">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                    @if (count($requirements) === 1)
                        <p class="muted mt-4">No other requirements were set. This is open to everyone in the
                            location above.</p>
                    @endif
                </section>

                @if ($details)
                    <section class="card-lg border-2 border-[var(--navy)]">
                        <h2 class="h3">Details</h2>
                        <dl class="detail-list mt-4">
                            @foreach ($details as $label => $value)
                                <div class="{{ strlen($value) > 60 ? 'sm:col-span-2' : '' }}">
                                    <dt>{{ $label }}</dt>
                                    <dd class="text-block">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>
                @endif
            </div>

            <div class="lg:sticky lg:top-6 lg:self-start">
                <x-seeker.apply-panel :listing="$listing" :applied="$applied" />
            </div>
        </div>
    </div>
@endsection

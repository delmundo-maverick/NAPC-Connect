@extends('layouts.app')
@section('title', 'Applicants · NAPC Connect')

@section('navbar')
    <x-navbar.poster />
@endsection

@section('content')
    <x-page-header title="Applicants" :subtitle="$listing->title">
        <x-slot:actions>
            <a href="{{ route('poster.listings.edit', $listing) }}" class="btn-ghost border-2 border-[var(--navy)]">Edit
                listing</a>
            <a href="{{ route('poster.listings.index') }}" class="btn-ghost border-2 border-[var(--navy)]">Back to
                listings</a>
        </x-slot:actions>
    </x-page-header>

    <div class="container-page space-y-6 py-10">

        @if ($listing->apply_mode === 'link')
            <x-alert type="warning">This listing sends applicants to your own form, so their answers are not stored
                here.</x-alert>
        @elseif ($listing->apply_mode === 'info')
            <x-alert type="warning">This listing has no online application. Applicants follow your instructions
                instead.</x-alert>
        @endif

        <p class="muted font-semibold">
            {{ $applications->total() }} {{ \Illuminate\Support\Str::plural('application', $applications->total()) }}
        </p>

        @forelse ($applications as $application)
            @php
                $profileRows = $application->user->seekerProfile?->detailRows($listing->income_max !== null) ?? [];
            @endphp

            <article class="card-lg border-2 border-[var(--navy)]">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <span
                            class="avatar shrink-0">{{ mb_strtoupper(mb_substr($application->user->first_name, 0, 1)) }}</span>
                        <div class="min-w-0">
                            <h3 class="h3 truncate">{{ $application->user->name }}</h3>
                            <p class="muted truncate">{{ $application->user->email }}</p>
                        </div>
                    </div>
                    <span class="muted font-semibold">Applied {{ $application->created_at->format('M j, Y') }}</span>
                </div>

                @if ($profileRows)
                    <p class="label mt-5">Profile</p>
                    <dl class="detail-list">
                        @foreach ($profileRows as $label => $value)
                            <div>
                                <dt>{{ $label }}</dt>
                                <dd>{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif

                @if (!empty($listing->application_form))
                    <p class="label mt-5">Answers</p>
                    <dl class="detail-list">
                        @foreach ($listing->application_form as $question)
                            @php $answer = $application->answers[$question['key']] ?? null; @endphp
                            <div class="{{ in_array($question['type'], ['textarea'], true) ? 'sm:col-span-2' : '' }}">
                                <dt>{{ $question['label'] }}</dt>
                                <dd>{{ is_array($answer) ? implode(', ', $answer) : ($answer !== null && $answer !== '' ? $answer : 'No answer') }}
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </article>
        @empty
            <x-empty-state title="No applications yet">When seekers apply through your form, their answers show up
                here.</x-empty-state>
        @endforelse

        {{ $applications->links('pagination.brand') }}
    </div>
@endsection

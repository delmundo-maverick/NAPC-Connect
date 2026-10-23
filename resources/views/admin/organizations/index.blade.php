@extends('layouts.app')
@section('title', 'Organizations · Admin · NAPC Connect')


@section('navbar')
    <x-navbar.admin />
@endsection
@php
    $options = [
        'pending' => 'Pending (' . $counts['pending'] . ')',
        'approved' => 'Approved (' . $counts['approved'] . ')',
        'rejected' => 'Rejected (' . $counts['rejected'] . ')',
        'all' => 'All (' . $counts['all'] . ')',
    ];
@endphp

@section('content')
    <x-page-header title="Organizations"
        subtitle="Approve or reject the schools, LGUs, agencies and companies that want to post." />

    <div class="container-page space-y-6 py-10">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <x-filter-tabs :options="$options" :current="$status" param="status" />
            <x-search-box placeholder="Search organization, name or email" :keep="['status']" />
        </div>

        <p class="muted font-semibold">
            {{ $orgs->total() }} {{ \Illuminate\Support\Str::plural('result', $orgs->total()) }}
            @if ($search !== '')
                for “{{ $search }}”
            @endif
        </p>

        <div class="space-y-6">
            @forelse ($orgs as $org)
                <x-org-card :org="$org" />
            @empty
                <x-empty-state title="No organizations found">Try another status or a different search.</x-empty-state>
            @endforelse
        </div>

        {{ $orgs->links('pagination.brand') }}
    </div>
@endsection

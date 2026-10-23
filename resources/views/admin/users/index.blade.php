@extends('layouts.app')
@section('title', 'Users · Admin · NAPC Connect')


@section('navbar')
    <x-navbar.admin />
@endsection
@php
    $options = [
        'all' => 'All (' . $counts['all'] . ')',
        'seeker' => 'Seekers (' . $counts['seeker'] . ')',
        'poster' => 'Organizations (' . $counts['poster'] . ')',
        'admin' => 'Admins (' . $counts['admin'] . ')',
    ];
@endphp

@section('content')
    <x-page-header title="Users" subtitle="Everyone who has an account on NAPC Connect." />

    <div class="container-page space-y-6 py-10">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <x-filter-tabs :options="$options" :current="$role" param="role" />
            <x-search-box placeholder="Search name, email or organization" :keep="['role']" />
        </div>

        <p class="muted font-semibold">
            {{ $users->total() }} {{ \Illuminate\Support\Str::plural('result', $users->total()) }}
            @if ($search !== '')
                for “{{ $search }}”
            @endif
        </p>

        @if ($users->isEmpty())
            <x-empty-state title="No users found">Try another filter or a different search.</x-empty-state>
        @else
            <ul class="list-card">
                @foreach ($users as $account)
                    <x-user-row :user="$account" />
                @endforeach
            </ul>
        @endif

        {{ $users->links('pagination.brand') }}
    </div>
@endsection

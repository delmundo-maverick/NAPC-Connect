@extends('layouts.auth')
@section('title', 'Log in')

@section('content')
    <h1 class="h2">Welcome back</h1>
    <p class="muted mt-2 text-base">Log in to see programs picked for you.</p>

    @if (session('status'))
        <p class="mt-4 rounded-lg bg-[var(--mist)] p-3 text-sm font-semibold text-[var(--navy)]">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf
        <x-field name="email" type="email" label="Email" autocomplete="email" />
        <x-field name="password" type="password" label="Password" autocomplete="current-password" />

        <label class="flex items-center gap-2 text-sm font-semibold">
            <input type="checkbox" name="remember" value="1" class="h-4 w-4 accent-[var(--navy)]">
            Keep me logged in
        </label>

        <button type="submit" class="btn-primary w-full">Log in</button>
    </form>

    <p class="muted mt-6 text-center text-base">
        New here? <a href="{{ route('register') }}" class="font-bold text-[var(--sky)] underline">Create an account</a>
    </p>
@endsection

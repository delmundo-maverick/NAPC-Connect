@extends('layouts.auth')
@section('title', 'Create your account')

@section('content')
    <div x-data="registerForm('{{ old('role', $role) }}')">
        <h1 class="h2">Create your account</h1>
        <p class="muted mt-2 text-base">It’s free. You can browse without an account, but an account lets us suggest programs
            that fit you.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
            @csrf
            <input type="hidden" name="role" :value="role">

            {{-- Role picker --}}
            <fieldset>
                <legend class="label">I am</legend>
                <div class="grid gap-3 sm:grid-cols-2">
                    <label
                        class="cursor-pointer rounded-xl border-2 border-[var(--navy)] p-4 focus-within:ring-4 focus-within:ring-[var(--sky)]"
                        :class="cardClass('seeker')">
                        <input type="radio" name="role_choice" value="seeker" x-model="role" class="sr-only">
                        <span class="block font-extrabold">Looking for opportunities</span>
                        <span class="mt-1 block text-sm opacity-80">Students, job seekers, families</span>
                    </label>

                    <label
                        class="cursor-pointer rounded-xl border-2 border-[var(--navy)] p-4 focus-within:ring-4 focus-within:ring-[var(--sky)]"
                        :class="cardClass('poster')">
                        <input type="radio" name="role_choice" value="poster" x-model="role" class="sr-only">
                        <span class="block font-extrabold">Posting opportunities</span>
                        <span class="mt-1 block text-sm opacity-80">Schools, LGUs, agencies, recruiters, NGOs</span>
                    </label>
                </div>
                @error('role')
                    <p class="mt-1 text-sm font-semibold text-[var(--coral)]">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Everyone --}}
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="first_name" label="First name" autocomplete="given-name" />
                <x-field name="last_name" label="Last name" autocomplete="family-name" />
            </div>
            <x-field name="middle_name" label="Middle name (optional)" autocomplete="additional-name" />
            <x-field name="email" type="email" label="Email" autocomplete="email" />
            <x-field name="password" type="password" label="Password" hint="At least 8 characters."
                autocomplete="new-password" />
            <x-field name="password_confirmation" type="password" label="Confirm password" autocomplete="new-password" />

            {{-- Posters only --}}
            <div x-show="isPoster" x-cloak class="space-y-5 rounded-xl bg-[var(--mist)] p-5">
                <p class="text-sm font-semibold text-[var(--navy)]">Tell us about your organization. An admin reviews this
                    before you can publish listings.</p>

                <x-field name="organization" label="Organization name" x-bind:disabled="!isPoster" />

                <div>
                    <label for="organization_type" class="label">Organization type</label>
                    <select id="organization_type" name="organization_type" class="input" x-bind:disabled="!isPoster">
                        <option value="">Choose one</option>
                        @foreach (['school' => 'School or university', 'lgu' => 'Local government unit (LGU)', 'agency' => 'Government agency', 'company' => 'Company or recruiter', 'ngo' => 'NGO or non-profit'] as $value => $text)
                            <option value="{{ $value }}" @selected(old('organization_type') === $value)>{{ $text }}</option>
                        @endforeach
                    </select>
                    @error('organization_type')
                        <p class="mt-1 text-sm font-semibold text-[var(--coral)]">{{ $message }}</p>
                    @enderror
                </div>

                <x-field name="position" label="Your position" x-bind:disabled="!isPoster" />
                <x-field name="proof_url" type="url" label="Link that proves your organization"
                    hint="An official website or page that lists your organization." x-bind:disabled="!isPoster" />
            </div>

            <button type="submit" class="btn-primary w-full">Create account</button>
        </form>

        <p class="muted mt-6 text-center text-base">
            Already have an account? <a href="{{ route('login') }}" class="font-bold text-[var(--sky)] underline">Log in</a>
        </p>
    </div>
@endsection

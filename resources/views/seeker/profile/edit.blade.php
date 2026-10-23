@extends('layouts.app')
@section('title', 'My profile · NAPC Connect')

@section('navbar')
    <x-navbar.seeker />
@endsection

@php
    $config = [
        'region' => old('region', $profile->region),
        'province' => old('province', $profile->province),
        'yearLevel' => old('year_level', $profile->year_level),
        'regions' => collect(config('ph_locations'))
            ->map(fn($r) => ['name' => $r['name'], 'provinces' => $r['provinces']])
            ->all(),
    ];
@endphp

@section('content')
    <x-page-header title="My profile"
        subtitle="We use this to suggest programs you can actually apply for. Everything is optional.">
        <x-slot:actions>
            <a href="{{ route('seeker.home') }}" class="btn-ghost border-2 border-[var(--navy)]">Back to home</a>
        </x-slot:actions>
    </x-page-header>

    <div class="container-page py-10">
        <div class="mx-auto max-w-3xl">
            <form method="POST" action="{{ route('seeker.profile.update') }}" class="space-y-8" x-data="profileForm"
                data-config="{{ json_encode($config) }}">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <x-alert type="danger">
                        <p class="font-extrabold">Please fix these first:</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                {{-- 1. ABOUT YOU --}}
                <x-seeker.form-section number="1" title="About you">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-field name="birthdate" type="date" label="Birthdate" :value="$profile->birthdate?->format('Y-m-d')"
                            hint="Used to match age limits. Organizations you apply to see your age, not your birthdate." />
                        <x-field name="income_monthly" type="number" label="Monthly household income (PHP)"
                            :value="$profile->income_monthly" min="0"
                            hint="For programs with an income limit. Organizations only see it when their listing has one." />
                    </div>

                    <x-seeker.check-group name="sectors" label="Do any of these describe you?" :options="config('napc.sectors')"
                        :selected="$profile->sectors ?? []" hint="Tick all that apply. Leave empty if none." />
                </x-seeker.form-section>

                {{-- 2. LOCATION --}}
                <x-seeker.form-section number="2" title="Where you live">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="region" class="label">Region</label>
                            <select id="region" name="region" class="input" x-model="region" @change="regionChanged()">
                                <option value="">Choose a region</option>
                                @foreach (config('ph_locations') as $key => $r)
                                    <option value="{{ $key }}">{{ $r['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="province" class="label">Province</label>
                            <select id="province" name="province" class="input" :disabled="!region"
                                @change="province = $event.target.value">
                                <option value="" :selected="province === ''">Choose a province</option>
                                <template x-for="name in provinces" :key="name">
                                    <option :value="name" :selected="name === province" x-text="name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <x-field name="city" label="City or municipality" :value="$profile->city" placeholder="e.g. Lipa City"
                        hint="Pick a region first. Helps with LGU programs." x-bind:disabled="!region" />
                </x-seeker.form-section>

                {{-- 3. SCHOOL --}}
                <x-seeker.form-section number="3" title="Your schooling">
                    <div>
                        <label for="year_level" class="label">Current year level</label>
                        <select id="year_level" name="year_level" class="input" x-model="yearLevel">
                            <option value="">Choose one</option>
                            <option value="none">Not currently in school</option>
                            @foreach (config('napc.year_levels') as $value => $text)
                                <option value="{{ $value }}">{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="isStudent" x-cloak class="space-y-6">
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="school_type" class="label">School type</label>
                                <select id="school_type" name="school_type" class="input">
                                    <option value="">Choose one</option>
                                    @foreach (config('napc.school_types') as $value => $text)
                                        <option value="{{ $value }}" @selected(old('school_type', $profile->school_type) === $value)>
                                            {{ $text }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <x-field name="school_name" label="School name" :value="$profile->school_name" maxlength="150"
                                placeholder="e.g. Batangas State University" />
                        </div>

                        <x-field name="course" label="Course, strand or field" :value="$profile->course" maxlength="100"
                            placeholder="e.g. BSIT, BS Nursing or STEM" />
                    </div>
                </x-seeker.form-section>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('seeker.home') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-accent">Save profile</button>
                </div>
            </form>
        </div>
    </div>
@endsection

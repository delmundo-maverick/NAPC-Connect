@extends('layouts.app')
@section('title', 'Apply · NAPC Connect')

@section('navbar')
    <x-navbar.seeker />
@endsection

@section('content')
    <x-page-header title="Apply" :subtitle="$listing->title">
        <x-slot:actions>
            <a href="{{ route('seeker.listings.show', $listing) }}" class="btn-ghost border-2 border-[var(--navy)]">Back to
                listing</a>
        </x-slot:actions>
    </x-page-header>

    <div class="container-page py-10">
        <div class="mx-auto max-w-3xl">
            <form method="POST" action="{{ route('seeker.applications.store', $listing) }}" class="space-y-8">
                @csrf

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

                {{-- 1. YOUR DETAILS --}}
                <x-seeker.form-section number="1" title="Your details"
                    hint="The organization sees these, together with the profile details you filled in and your answers.">
                    <dl class="detail-list">
                        <div>
                            <dt>Name</dt>
                            <dd>{{ auth()->user()->name }}</dd>
                        </div>
                        <div>
                            <dt>Email</dt>
                            <dd>{{ auth()->user()->email }}</dd>
                        </div>
                    </dl>
                    <p class="muted">Something outdated? <a href="{{ route('seeker.profile.edit') }}"
                            class="font-bold underline">Update your profile</a> first, then come back.</p>
                </x-seeker.form-section>

                {{-- 2. QUESTIONS --}}
                <x-seeker.form-section number="2" title="Questions" hint="Fields marked with * are required.">

                    @forelse ($listing->application_form ?? [] as $q)
                        @php
                            $key = $q['key'];
                            $field = "answers.{$key}";
                            $id = "a_{$key}";
                            $name = "answers[{$key}]";
                            $required = !empty($q['required']);
                            $value = old($field);
                        @endphp

                        @if (in_array($q['type'], ['radio', 'checkbox'], true))
                            <fieldset>
                                <legend class="label">{{ $q['label'] }} @if ($required)
                                        <span aria-hidden="true">*</span>
                                    @endif
                                </legend>
                                <div class="check-grid">
                                    @foreach ($q['options'] ?? [] as $i => $option)
                                        <label class="check-item">
                                            @if ($q['type'] === 'radio')
                                                <input type="radio" name="{{ $name }}" value="{{ $option }}"
                                                    @checked($value === $option)>
                                            @else
                                                <input type="checkbox" name="{{ $name }}[]"
                                                    value="{{ $option }}" @checked(in_array($option, (array) old($field, []), true))>
                                            @endif
                                            <span>{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error($field)
                                    <p class="field-error">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        @else
                            <div>
                                <label class="label" for="{{ $id }}">{{ $q['label'] }} @if ($required)
                                        <span aria-hidden="true">*</span>
                                    @endif
                                </label>

                                @if ($q['type'] === 'textarea')
                                    <textarea id="{{ $id }}" name="{{ $name }}" rows="5" maxlength="3000" class="input">{{ $value }}</textarea>
                                @elseif ($q['type'] === 'select')
                                    <select id="{{ $id }}" name="{{ $name }}" class="input">
                                        <option value="">Choose one</option>
                                        @foreach ($q['options'] ?? [] as $option)
                                            <option value="{{ $option }}" @selected($value === $option)>
                                                {{ $option }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($q['type'] === 'number')
                                    <input type="number" step="any" id="{{ $id }}"
                                        name="{{ $name }}" value="{{ $value }}" class="input">
                                @elseif ($q['type'] === 'date')
                                    <input type="date" id="{{ $id }}" name="{{ $name }}"
                                        value="{{ $value }}" class="input">
                                @else
                                    <input type="text" id="{{ $id }}" name="{{ $name }}"
                                        value="{{ $value }}" maxlength="500" class="input">
                                @endif

                                @error($field)
                                    <p class="field-error">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    @empty
                        <p class="muted">This listing has no extra questions. Just send your application.</p>
                    @endforelse
                </x-seeker.form-section>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('seeker.listings.show', $listing) }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-accent">Send application</button>
                </div>
            </form>
        </div>
    </div>
@endsection

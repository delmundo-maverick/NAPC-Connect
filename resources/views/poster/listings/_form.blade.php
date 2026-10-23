@php
    // Questions: after a failed submit use what was typed, otherwise what is saved
    $questions = collect(old('questions', $listing->application_form ?? []))
        ->map(
            fn($q) => [
                'key' => $q['key'] ?? null,
                'label' => $q['label'] ?? '',
                'type' => $q['type'] ?? 'text',
                'required' => !empty($q['required']),
                'optionsText' => is_array($q['options'] ?? null) ? implode("\n", $q['options']) : $q['options'] ?? '',
            ],
        )
        ->values()
        ->all();

    $config = [
        'type' => old('type', $listing->type ?? 'scholarship'),
        'applyMode' => old('apply_mode', $listing->apply_mode ?? 'info'),
        'details' => old('details', $listing->details ?? []),
        'targeting' => config('napc.targeting'),
        'detailFields' => collect(config('napc.detail_fields'))
            ->map(fn($fields) => collect($fields)->map(fn($f, $key) => ['key' => $key] + $f)->values()->all())
            ->all(),
    ];

    $status = $listing->exists ? $listing->status : 'draft';
    $takenDown = $listing->exists && $listing->isTakenDown();
@endphp

<form method="POST" action="{{ $action }}" class="space-y-8" x-data="listingForm"
    data-config="{{ json_encode($config) }}">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

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

    {{-- 1. BASICS --}}
    <x-poster.form-section number="1" title="The basics">
        <fieldset>
            <legend class="label">What are you posting?</legend>
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach (config('napc.types') as $value => $text)
                    <label class="type-card type-card-{{ $value }}"
                        :class="type === '{{ $value }}' ? 'type-card-on' : ''">
                        <input type="radio" name="type" value="{{ $value }}" x-model="type" class="sr-only">
                        {{ $text }}
                    </label>
                @endforeach
            </div>
        </fieldset>

        <x-field name="title" label="Title" :value="$listing->title" maxlength="200"
            placeholder="e.g. Tertiary Education Subsidy for 2nd year students" />

        <x-poster.textarea name="description" label="Description" :value="$listing->description" rows="6"
            hint="What is it, who is it for, and what do applicants get?" />

        <div class="grid gap-6 sm:grid-cols-2">
            <x-field name="deadline" type="date" label="Deadline (optional)" :value="$listing->deadline?->format('Y-m-d')"
                hint="Leave blank if applications stay open." />
            <x-field name="source_url" type="url" label="Official announcement link (optional)" :value="$listing->source_url"
                placeholder="https://" hint="Where this was announced. Seekers see it as proof." />
        </div>
    </x-poster.form-section>

    {{-- 2. WHO CAN APPLY --}}
    <x-poster.form-section number="2" title="Who can apply"
        hint="Everything here is optional. Leave a field blank to keep it open to everyone. We match seekers using their profile, so the more you fill in, the better the suggestions.">

        {{-- Location --}}
        <div x-show="shows('location')" x-cloak class="space-y-2">
            <x-location-picker :region="old('region', $listing->region)" :province="old('province', $listing->province)" :city="old('city', $listing->city)"
                region-label="Where is this available?" region-placeholder="Nationwide" province-label="Province"
                province-placeholder="Whole region" city-label="City or municipality"
                city-placeholder="Any city or municipality" />
            <p class="muted">Leave the region on Nationwide to open it to everyone. Pick a city for LGU programs, like
                Lipa City.</p>
        </div>

        <div x-show="shows('year_levels')" x-cloak>
            <x-poster.check-group name="year_levels" label="Year level" :options="config('napc.year_levels')" :selected="$listing->year_levels ?? []"
                hint="Tick every level that can apply." />
        </div>

        <div x-show="shows('school_types')" x-cloak>
            <x-poster.check-group name="school_types" label="School type" :options="config('napc.school_types')" :selected="$listing->school_types ?? []" />
        </div>

        <div x-show="shows('courses')" x-cloak>
            <label for="course_draft" class="label">Courses, strands or fields</label>
            <div x-data="tagInput" data-name="courses" class="tag-box" @click="$refs.field.focus()"
                data-initial="{{ json_encode(array_values((array) old('courses', $listing->courses ?? []))) }}">
                <template x-for="(tag, i) in tags" :key="tag">
                    <span class="tag">
                        <span x-text="tag"></span>
                        <button type="button" @click.stop="remove(i)" :aria-label="'Remove ' + tag">&times;</button>
                        <input type="hidden" :name="name + '[]'" :value="tag">
                    </span>
                </template>
                <input id="course_draft" x-ref="field" x-model="draft" type="text" class="tag-field"
                    autocomplete="off" placeholder="Type a course, then press Enter" @keydown.enter.prevent="add()"
                    @keydown.comma.prevent="add()" @keydown.backspace="backspace()" @blur="add()">
            </div>
            <p class="muted mt-1">For example BSIT, BS Nursing or STEM. Leave empty for any course.</p>
        </div>

        <div x-show="shows('sectors')" x-cloak>
            <x-poster.check-group name="sectors" label="Sector or group" :options="config('napc.sectors')" :selected="$listing->sectors ?? []"
                hint="Tick the groups this is meant for." />
        </div>

        <div x-show="shows('age')" x-cloak class="grid gap-4 sm:grid-cols-2">
            <x-field name="age_min" type="number" label="Minimum age" :value="$listing->age_min" min="10"
                max="100" />
            <x-field name="age_max" type="number" label="Maximum age" :value="$listing->age_max" min="10"
                max="100" />
        </div>

        <div x-show="shows('income')" x-cloak>
            <x-field name="income_max" type="number" label="Maximum household income per month (PHP)"
                :value="$listing->income_max" min="0" hint="Leave blank if there is no income limit." />
        </div>

        <x-poster.textarea name="other_requirements" label="Other requirements (optional)" :value="$listing->other_requirements"
            rows="3" hint="Anything else applicants must meet that the fields above don’t cover." />
    </x-poster.form-section>

    {{-- 3. DETAILS --}}
    <x-poster.form-section number="3" title="Extra details"
        hint="Add only the details your applicants need. Tap a detail to add it, tap again to remove it.">

        <div class="flex flex-wrap gap-2">
            <template x-for="field in activeDetails" :key="field.key">
                <button type="button" :class="hasDetail(field.key) ? 'chip-active' : 'chip'"
                    @click="toggleDetail(field.key)" x-text="field.label"></button>
            </template>
        </div>

        <p class="muted" x-show="chosenDetailFields.length === 0" x-cloak>No extra details added yet.</p>

        <div class="space-y-5">
            <template x-for="field in chosenDetailFields" :key="field.key">
                <div>
                    <label class="label" :for="'detail_' + field.key" x-text="field.label"></label>

                    <template x-if="field.input === 'textarea'">
                        <textarea :id="'detail_' + field.key" :name="'details[' + field.key + ']'" rows="3" class="input"
                            x-model="detailValues[field.key]" :placeholder="field.hint || ''"></textarea>
                    </template>

                    <template x-if="field.input === 'select'">
                        <select :id="'detail_' + field.key" :name="'details[' + field.key + ']'" class="input"
                            @change="detailValues[field.key] = $event.target.value">
                            <option value="">Choose one</option>
                            <template x-for="option in field.options" :key="option">
                                <option :value="option" :selected="detailValues[field.key] === option"
                                    x-text="option"></option>
                            </template>
                        </select>
                    </template>

                    <template x-if="field.input === 'text'">
                        <input type="text" :id="'detail_' + field.key" :name="'details[' + field.key + ']'"
                            class="input" x-model="detailValues[field.key]" :placeholder="field.hint || ''">
                    </template>
                </div>
            </template>
        </div>
    </x-poster.form-section>

    {{-- 4. HOW TO APPLY --}}
    <x-poster.form-section number="4" title="How to apply">
        <fieldset>
            <legend class="label">How will seekers apply?</legend>
            <div class="grid gap-3 md:grid-cols-3">
                @foreach ([
        'form' => ['Use an NAPC Connect form', 'Build your own questions. Applicants answer them here.'],
        'link' => ['Link to my own form', 'Paste a Google Forms, Microsoft Forms or other link.'],
        'info' => ['No online application', 'Applicants follow your instructions, like visiting an office.'],
    ] as $value => [$title, $text])
                    <label class="mode-card" :class="applyMode === '{{ $value }}' ? 'mode-card-on' : ''">
                        <input type="radio" name="apply_mode" value="{{ $value }}" x-model="applyMode"
                            class="sr-only">
                        <span class="block font-extrabold">{{ $title }}</span>
                        <span class="mt-1 block text-sm opacity-80">{{ $text }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        {{-- Question builder (only when using an NAPC Connect form) --}}
        <template x-if="applyMode === 'form'">
            <div x-data="questionBuilder" class="space-y-4" data-initial="{{ json_encode($questions) }}"
                data-types="{{ json_encode(config('napc.question_types')) }}"
                data-choice="{{ json_encode(config('napc.choice_types')) }}">

                <p class="muted">Every application automatically includes the applicant’s name, email and profile
                    details. Add only the extra questions you need.</p>

                <ol class="space-y-4">
                    <template x-for="(q, i) in questions" :key="q.uid">
                        <li class="question-card">
                            <input type="hidden" :name="`questions[${i}][key]`" :value="q.key">

                            <div class="question-grid space-y-4 sm:space-y-0">
                                <div>
                                    <label class="label" :for="`q_${q.uid}`">Question <span
                                            x-text="i + 1"></span></label>
                                    <input type="text" :id="`q_${q.uid}`" :name="`questions[${i}][label]`"
                                        x-model="q.label" class="input" maxlength="160"
                                        placeholder="e.g. Why are you applying?">
                                </div>
                                <div>
                                    <label class="label" :for="`t_${q.uid}`">Answer type</label>
                                    <select :id="`t_${q.uid}`" :name="`questions[${i}][type]`" class="input"
                                        @change="q.type = $event.target.value">
                                        <template x-for="(text, value) in types" :key="value">
                                            <option :value="value" :selected="q.type === value"
                                                x-text="text"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <template x-if="isChoice(q)">
                                <div class="mt-4">
                                    <label class="label" :for="`o_${q.uid}`">Choices (one per line)</label>
                                    <textarea :id="`o_${q.uid}`" :name="`questions[${i}][options]`" x-model="q.optionsText" rows="3"
                                        class="input" placeholder="Option 1&#10;Option 2"></textarea>
                                </div>
                            </template>

                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                <label class="flex items-center gap-2 text-sm font-bold text-[var(--navy)]">
                                    <input type="checkbox" :name="`questions[${i}][required]`" value="1"
                                        x-model="q.required" class="h-4 w-4 accent-[var(--navy)]">
                                    Required
                                </label>
                                <div class="flex gap-2">
                                    <button type="button" class="btn-ghost btn-sm" @click="move(i, -1)"
                                        :disabled="i === 0">Up</button>
                                    <button type="button" class="btn-ghost btn-sm" @click="move(i, 1)"
                                        :disabled="i === questions.length - 1">Down</button>
                                    <button type="button" class="btn-danger btn-sm"
                                        @click="remove(i)">Remove</button>
                                </div>
                            </div>
                        </li>
                    </template>
                </ol>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" class="btn-primary btn-sm" @click="add()">Add a question</button>
                    <span class="muted">or quick add:</span>
                    <template x-for="preset in presets" :key="preset.label">
                        <button type="button" class="chip-soft" @click="add(preset)"
                            x-text="preset.label"></button>
                    </template>
                </div>
            </div>
        </template>

        {{-- Own form link --}}
        <div x-show="applyMode === 'link'" x-cloak>
            <x-field name="apply_url" type="url" label="Link to your form" :value="$listing->apply_url"
                placeholder="https://forms.gle/..." hint="Seekers open this link in a new tab."
                x-bind:disabled="applyMode !== 'link'" />
        </div>

        <x-poster.textarea name="how_to_apply" label="Instructions" :value="$listing->how_to_apply" rows="4"
            hint="Required when there is no online application. Otherwise add anything applicants should know, like documents to bring." />
    </x-poster.form-section>

    {{-- ACTIONS --}}
    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('poster.listings.index') }}" class="btn-ghost">Cancel</a>

        <div class="flex flex-col gap-3 sm:flex-row">
            @if ($status === 'published')
                <button type="submit" name="action" value="save" class="btn-primary">Save changes</button>
            @elseif ($takenDown)
                <button type="submit" name="action" value="save" class="btn-primary">Save changes</button>
            @else
                <button type="submit" name="action" value="save"
                    class="btn-ghost border-2 border-[var(--navy)]">
                    {{ $status === 'closed' ? 'Save changes' : 'Save as draft' }}
                </button>
                <button type="submit" name="action" value="publish" class="btn-accent">
                    {{ $status === 'closed' ? 'Reopen and publish' : 'Publish' }}
                </button>
            @endif
        </div>
    </div>
</form>

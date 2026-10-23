<?php

namespace App\Http\Requests\Poster;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\City;

class ListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'poster';
    }

    public function rules(): array
    {
        $isForm = $this->input('apply_mode') === 'form';

        $rules = [
            'action'      => ['required', Rule::in(['save', 'publish'])],
            'type'        => ['required', Rule::in(array_keys(config('napc.types')))],
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:5000'],
            'deadline'    => ['nullable', 'date', Rule::when($this->input('action') === 'publish', ['after_or_equal:today'])],
            'source_url'  => ['nullable', 'url:http,https', 'max:500'],

            'region'   => ['nullable', Rule::exists('psgc_regions', 'code')],
            'province' => ['nullable', Rule::exists('psgc_provinces', 'code')->where('region_code', $this->input('region'))],
            'city'     => ['nullable', Rule::exists('psgc_cities', 'code')->where('region_code', $this->input('region'))],

            'year_levels'    => ['nullable', 'array'],
            'year_levels.*'  => [Rule::in(array_keys(config('napc.year_levels')))],
            'school_types'   => ['nullable', 'array'],
            'school_types.*' => [Rule::in(array_keys(config('napc.school_types')))],
            'sectors'        => ['nullable', 'array'],
            'sectors.*'      => [Rule::in(array_keys(config('napc.sectors')))],
            'courses'        => ['nullable', 'array', 'max:15'],
            'courses.*'      => ['string', 'max:80'],

            'age_min'    => ['nullable', 'integer', 'between:10,100'],
            'age_max'    => ['nullable', 'integer', 'between:10,100'],
            'income_max' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'other_requirements' => ['nullable', 'string', 'max:2000'],

            'details'   => ['nullable', 'array'],
            'details.*' => ['nullable', 'string', 'max:1000'],

            'apply_mode'   => ['required', Rule::in(['form', 'link', 'info'])],
            'apply_url'    => ['required_if:apply_mode,link', 'nullable', 'url:http,https', 'max:500'],
            'how_to_apply' => ['required_if:apply_mode,info', 'nullable', 'string', 'max:3000'],

            'questions' => $isForm ? ['required', 'array', 'min:1', 'max:20'] : ['nullable', 'array'],
        ];

        if ($isForm) {
            $rules += [
                'questions.*.key'      => ['nullable', 'regex:/^q_[a-z0-9]{6}$/'],
                'questions.*.label'    => ['required', 'string', 'max:160'],
                'questions.*.type'     => ['required', Rule::in(array_keys(config('napc.question_types')))],
                'questions.*.required' => ['nullable', 'boolean'],
                'questions.*.options'  => ['nullable', 'string', 'max:1500'],
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'source_url'         => 'announcement link',
            'apply_url'          => 'form link',
            'how_to_apply'       => 'instructions',
            'age_min'            => 'minimum age',
            'age_max'            => 'maximum age',
            'income_max'         => 'income limit',
            'questions.*.label'  => 'question',
            'questions.*.type'   => 'answer type',
        ];
    }

    public function messages(): array
    {
        return [
            'questions.required' => 'Add at least one question, or choose a different way to apply.',
            'questions.min'      => 'Add at least one question, or choose a different way to apply.',
            'province.exists'    => 'That province is not in the selected region.',
            'city.exists'        => 'That city is not in the selected region.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // A city must belong to the chosen province. Independent cities have none and fit any province.
            $province = $this->input('province');
            $city = $this->input('city');

            if ($province && $city && ! $validator->errors()->hasAny(['province', 'city'])) {
                $cityProvince = City::whereKey($city)->value('province_code');

                if ($cityProvince !== null && $cityProvince !== $province) {
                    $validator->errors()->add('city', 'That city is not in the selected province.');
                }
            }

            $min = $this->input('age_min');
            $max = $this->input('age_max');
            if ($min !== null && $max !== null && (int) $max < (int) $min) {
                $validator->errors()->add('age_max', 'The maximum age must be the same as or higher than the minimum age.');
            }

            if ($this->input('apply_mode') === 'form') {
                foreach ((array) $this->input('questions', []) as $i => $q) {
                    $isChoice = in_array($q['type'] ?? null, config('napc.choice_types'), true);

                    if ($isChoice && count($this->parseOptions($q['options'] ?? '')) < 2) {
                        $number = is_numeric($i) ? $i + 1 : $i;
                        $validator->errors()->add("questions.{$i}.options", "Question {$number} needs at least two choices, one per line.");
                    }
                }
            }
        });
    }

    /**
     * Clean data ready for Listing::create() / update().
     * Fields that do not apply to the chosen type are dropped.
     */
    public function payload(): array
    {
        $data = $this->validated();
        $type = $data['type'];
        $uses = fn(string $field): bool => in_array($field, config("napc.targeting.{$type}", []), true);
        $hasRegion = $uses('location') && ! empty($data['region']);

        return [
            'type'        => $type,
            'title'       => trim($data['title']),
            'description' => trim($data['description']),
            'deadline'    => $data['deadline'] ?? null,
            'source_url'  => $data['source_url'] ?? null,

            'region'   => $hasRegion ? $data['region'] : null,
            'province' => $hasRegion ? ($data['province'] ?? null) : null,
            'city'     => $hasRegion ? ($data['city'] ?? null) : null,

            'year_levels'  => $uses('year_levels') ? $this->cleanList($data['year_levels'] ?? []) : null,
            'school_types' => $uses('school_types') ? $this->cleanList($data['school_types'] ?? []) : null,
            'courses'      => $uses('courses') ? $this->cleanList($data['courses'] ?? []) : null,
            'sectors'      => $uses('sectors') ? $this->cleanList($data['sectors'] ?? []) : null,

            'age_min'    => $uses('age') && isset($data['age_min']) ? (int) $data['age_min'] : null,
            'age_max'    => $uses('age') && isset($data['age_max']) ? (int) $data['age_max'] : null,
            'income_max' => $uses('income') && isset($data['income_max']) ? (int) $data['income_max'] : null,
            'other_requirements' => $data['other_requirements'] ?? null,

            'details' => $this->details($type, $data['details'] ?? []),

            'apply_mode'       => $data['apply_mode'],
            'apply_url'        => $data['apply_mode'] === 'link' ? ($data['apply_url'] ?? null) : null,
            'how_to_apply'     => $data['how_to_apply'] ?? null,
            'application_form' => $data['apply_mode'] === 'form' ? $this->questions($data['questions'] ?? []) : null,
        ];
    }

    private function cleanList(array $items): ?array
    {
        $items = array_values(array_unique(array_filter(array_map(fn($i) => trim((string) $i), $items))));

        return $items ?: null;
    }

    /** Keep only the detail fields that exist for this type and have a value. */
    private function details(string $type, array $input): ?array
    {
        $clean = [];

        foreach (config("napc.detail_fields.{$type}", []) as $key => $field) {
            $value = trim((string) ($input[$key] ?? ''));

            if ($value === '') {
                continue;
            }
            if (($field['input'] ?? 'text') === 'select' && ! in_array($value, $field['options'] ?? [], true)) {
                continue;
            }

            $clean[$key] = $value;
        }

        return $clean ?: null;
    }

    private function questions(array $input): array
    {
        return collect($input)->map(function ($q) {
            $isChoice = in_array($q['type'], config('napc.choice_types'), true);

            return [
                'key'      => $q['key'] ?? ('q_' . Str::lower(Str::random(6))),
                'label'    => trim($q['label']),
                'type'     => $q['type'],
                'required' => (bool) ($q['required'] ?? false),
                'options'  => $isChoice ? $this->parseOptions($q['options'] ?? '') : [],
            ];
        })->values()->all();
    }

    private function parseOptions(?string $text): array
    {
        return collect(preg_split('/\R/', (string) $text))
            ->map(fn($line) => Str::limit(trim($line), 80, ''))
            ->filter()
            ->unique()
            ->take(12)
            ->values()
            ->all();
    }
}

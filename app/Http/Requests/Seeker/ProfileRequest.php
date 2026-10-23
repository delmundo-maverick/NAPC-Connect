<?php

namespace App\Http\Requests\Seeker;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\City;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'seeker';
    }

    public function rules(): array
    {
        return [
            'birthdate' => ['nullable', 'date', 'before_or_equal:-10 years', 'after_or_equal:-100 years'],

            'region'   => ['nullable', Rule::exists('psgc_regions', 'code')],
            'province' => ['nullable', Rule::exists('psgc_provinces', 'code')->where('region_code', $this->input('region'))],
            'city'     => ['nullable', Rule::exists('psgc_cities', 'code')->where('region_code', $this->input('region'))],

            'year_level'  => ['nullable', Rule::in(['none', ...array_keys(config('napc.year_levels'))])],
            'school_type' => ['nullable', Rule::in(array_keys(config('napc.school_types')))],
            'school_name' => ['nullable', 'string', 'max:150'],
            'course'      => ['nullable', 'string', 'max:100'],

            'sectors'   => ['nullable', 'array'],
            'sectors.*' => [Rule::in(array_keys(config('napc.sectors')))],

            'income_monthly' => ['nullable', 'integer', 'min:0', 'max:10000000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'birthdate'      => 'birthdate',
            'year_level'     => 'year level',
            'school_type'    => 'school type',
            'school_name'    => 'school name',
            'income_monthly' => 'monthly household income',
        ];
    }

    public function messages(): array
    {
        return [
            'birthdate.before_or_equal' => 'You must be at least 10 years old to use NAPC Connect.',
            'birthdate.after_or_equal'  => 'Please check your birthdate.',
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
        });
    }

    /** Clean data ready for updateOrCreate(). Fields that do not apply are cleared. */
    public function payload(): array
    {
        $data = $this->validated();

        $hasRegion = ! empty($data['region']);
        $isStudent = ! empty($data['year_level']) && $data['year_level'] !== 'none';

        $text = fn(string $key): ?string => ($v = trim((string) ($data[$key] ?? ''))) !== '' ? $v : null;

        $sectors = array_values(array_unique($data['sectors'] ?? []));

        return [
            'birthdate' => $data['birthdate'] ?? null,

            'region'   => $hasRegion ? $data['region'] : null,
            'province' => $hasRegion ? $text('province') : null,
            'city'     => $hasRegion ? $text('city') : null,

            'year_level'  => $data['year_level'] ?? null,
            'school_type' => $isStudent ? ($data['school_type'] ?? null) : null,
            'school_name' => $isStudent ? $text('school_name') : null,
            'course'      => $isStudent ? $text('course') : null,

            'sectors'        => $sectors ?: null,
            'income_monthly' => isset($data['income_monthly']) ? (int) $data['income_monthly'] : null,
        ];
    }
}

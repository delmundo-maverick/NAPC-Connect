<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\Locations;

class SeekerProfile extends Model
{
    protected $fillable = [
        'birthdate',
        'region',
        'province',
        'city',
        'year_level',
        'school_type',
        'school_name',
        'course',
        'sectors',
        'income_monthly',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'sectors'   => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function age(): ?int
    {
        return $this->birthdate?->age;
    }

    public function isStudent(): bool
    {
        return filled($this->year_level) && $this->year_level !== 'none';
    }

    /** 0 to 100, used by the "finish your profile" prompt */
    public function completion(): int
    {
        $fields = ['birthdate', 'region', 'year_level', 'income_monthly'];

        if ($this->isStudent()) {
            array_push($fields, 'school_type', 'course');
        }

        $filled = collect($fields)->filter(fn($field) => filled($this->{$field}))->count();

        return (int) round($filled / count($fields) * 100);
    }

    /** Label => value rows for an organization reading an application. */
    public function detailRows(bool $withIncome = false): array
    {
        $location = collect([
            Locations::city($this->city),
            Locations::province($this->province) ?: Locations::region($this->region),
        ])->filter()->implode(', ');

        $yearLevel = match (true) {
            $this->year_level === 'none' => 'Not currently in school',
            filled($this->year_level)    => config("napc.year_levels.{$this->year_level}"),
            default                      => null,
        };

        $school = collect([
            $this->school_name,
            $this->school_type ? config("napc.school_types.{$this->school_type}") : null,
        ])->filter()->implode(', ');

        $sectors = collect($this->sectors ?? [])
            ->map(fn($key) => config("napc.sectors.{$key}", $key))
            ->implode(', ');

        return array_filter([
            'Age'              => $this->age() !== null ? $this->age() . ' years old' : null,
            'Location'         => $location,
            'Year level'       => $yearLevel,
            'School'           => $school,
            'Course'           => $this->course,
            'Sector or group'  => $sectors,
            'Household income' => $withIncome && $this->income_monthly !== null
                ? 'PHP ' . number_format($this->income_monthly) . ' per month'
                : null,
        ], fn($value) => filled($value));
    }
}

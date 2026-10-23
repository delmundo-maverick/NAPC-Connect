<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Listing extends Model
{
    protected $fillable = [
        'type',
        'title',
        'description',
        'status',
        'published_at',
        'deadline',
        'source_url',
        'region',
        'province',
        'city',
        'year_levels',
        'school_types',
        'courses',
        'sectors',
        'age_min',
        'age_max',
        'income_max',
        'other_requirements',
        'details',
        'apply_mode',
        'apply_url',
        'how_to_apply',
        'application_form',
    ];

    protected function casts(): array
    {
        return [
            'deadline'         => 'date',
            'published_at'     => 'datetime',
            'year_levels'      => 'array',
            'school_types'     => 'array',
            'courses'          => 'array',
            'sectors'          => 'array',
            'details'          => 'array',
            'application_form' => 'array',
            'taken_down_at'    => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function typeLabel(): string
    {
        return config("napc.types.{$this->type}", ucfirst((string) $this->type));
    }

    public function locationLabel(): string
    {
        if (! $this->region) {
            return 'Nationwide';
        }

        $regionName = config("ph_locations.{$this->region}.name", $this->region);

        return collect([$this->city, $this->province ?: $regionName])->filter()->implode(', ');
    }

    public function isExpired(): bool
    {
        return $this->deadline !== null && $this->deadline->copy()->endOfDay()->isPast();
    }

    /** Removed by an admin. The organization cannot publish it again until it is restored. */
    public function isTakenDown(): bool
    {
        return $this->taken_down_at !== null;
    }

    public function deadlineLabel(): string
    {
        if (! $this->deadline) {
            return 'No deadline';
        }

        $date = $this->deadline->format('M j, Y');

        if ($this->isExpired()) {
            return "Deadline passed ({$date})";
        }

        $left = (int) now()->startOfDay()->diffInDays($this->deadline->copy()->startOfDay(), false);

        return match (true) {
            $left <= 0 => "Closes today ({$date})",
            $left === 1 => "Closes tomorrow ({$date})",
            default => "Closes {$date} ({$left} days left)",
        };
    }

    /** Published and not past its deadline: what seekers can browse */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn($q) => $q->whereNull('deadline')->orWhereDate('deadline', '>=', today()));
    }

    /** "Who can apply" as label => value, skipping anything left open */
    public function requirementRows(): array
    {
        $labels = fn(string $group, ?array $keys) => collect($keys ?? [])
            ->map(fn($key) => config("napc.{$group}.{$key}", $key))
            ->implode(', ');

        $age = match (true) {
            $this->age_min !== null && $this->age_max !== null => "{$this->age_min} to {$this->age_max} years old",
            $this->age_min !== null => "{$this->age_min} years old and above",
            $this->age_max !== null => "Up to {$this->age_max} years old",
            default => null,
        };

        return array_filter([
            'Location'           => $this->locationLabel(),
            'Year level'         => $labels('year_levels', $this->year_levels),
            'School type'        => $labels('school_types', $this->school_types),
            'Courses'            => collect($this->courses ?? [])->implode(', '),
            'Sector or group'    => $labels('sectors', $this->sectors),
            'Age'                => $age,
            'Household income'   => $this->income_max !== null
                ? 'Up to PHP ' . number_format($this->income_max) . ' per month'
                : null,
            'Other requirements' => $this->other_requirements,
        ], fn($value) => filled($value));
    }

    /** The extra details the organization added, with their labels */
    public function detailRows(): array
    {
        return collect($this->details ?? [])
            ->mapWithKeys(fn($value, $key) => [
                config("napc.detail_fields.{$this->type}.{$key}.label", ucfirst(str_replace('_', ' ', $key))) => $value,
            ])
            ->all();
    }
}

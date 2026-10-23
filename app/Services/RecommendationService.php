<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\SeekerProfile;
use App\Models\User;
use Illuminate\Support\Collection;
use App\Support\Locations;

class RecommendationService
{
    /**
     * Best matching open listings for a seeker.
     * Each listing gets two extra attributes: match_score and match_reasons.
     */
    public function forUser(User $user, int $limit = 6): Collection
    {
        $profile = $user->seekerProfile;

        if (! $profile) {
            return collect();
        }

        $applied = $user->applications()->pluck('listing_id');

        return Listing::open()
            ->with('user:id,organization')
            ->whereNotIn('id', $applied)
            ->latest('published_at')
            ->limit(300)
            ->get()
            ->map(fn(Listing $listing) => $this->match($listing, $profile))
            ->filter()
            ->sort(fn(Listing $a, Listing $b) => [-$a->match_score, $a->deadline?->getTimestamp() ?? PHP_INT_MAX]
                <=> [-$b->match_score, $b->deadline?->getTimestamp() ?? PHP_INT_MAX])
            ->values()
            ->take($limit);
    }

    /** Returns the listing with its score, or null when the seeker clearly does not qualify. */
    private function match(Listing $listing, SeekerProfile $profile): ?Listing
    {
        $score = 0;
        $reasons = [];

        // Location: nationwide listings fit everyone
        if ($listing->region && $profile->region) {
            if ($listing->region !== $profile->region) {
                return null;
            }

            $score += 2;
            $locationReason = 'Available in your region';

            if ($listing->province && $profile->province) {
                if ($listing->province !== $profile->province) {
                    return null;
                }

                $score += 1;
                $locationReason = 'Available in ' . (Locations::province($listing->province) ?: 'your province');
            }

            if ($listing->city && $profile->city) {
                if (mb_strtolower(trim($listing->city)) !== mb_strtolower(trim($profile->city))) {
                    return null;
                }

                $score += 2;
                $locationReason = 'For residents of ' . (Locations::city($listing->city) ?: 'your city');
            }

            $reasons[] = $locationReason;
        }

        // Year level ("none" means not in school, so it never matches)
        if (! empty($listing->year_levels) && $profile->year_level) {
            if (! in_array($profile->year_level, $listing->year_levels, true)) {
                return null;
            }

            $score += 3;
            $reasons[] = 'Matches your year level';
        }

        // School type
        if (! empty($listing->school_types) && $profile->school_type) {
            if (! in_array($profile->school_type, $listing->school_types, true)) {
                return null;
            }

            $score += 2;
            $reasons[] = 'Accepts your school type';
        }

        // Course: free text, so it only adds points and never hides a listing
        if (! empty($listing->courses) && filled($profile->course)) {
            $mine = mb_strtolower(trim($profile->course));

            $hit = collect($listing->courses)->first(function ($course) use ($mine) {
                $course = mb_strtolower(trim($course));

                return $course !== '' && (str_contains($mine, $course) || str_contains($course, $mine));
            });

            if ($hit) {
                $score += 3;
                $reasons[] = "Matches your course ({$hit})";
            }
        }

        // Sector or group: the seeker must belong to at least one
        if (! empty($listing->sectors)) {
            $common = array_values(array_intersect($listing->sectors, $profile->sectors ?? []));

            if ($common === []) {
                return null;
            }

            $score += 3 + (count($common) - 1);
            $labels = collect($common)->map(fn($key) => config("napc.sectors.{$key}", $key))->implode(', ');
            $reasons[] = "Meant for your group: {$labels}";
        }

        // Age
        $age = $profile->age();

        if ($age !== null && ($listing->age_min !== null || $listing->age_max !== null)) {
            if (($listing->age_min !== null && $age < $listing->age_min)
                || ($listing->age_max !== null && $age > $listing->age_max)
            ) {
                return null;
            }

            $score += 1;
            $reasons[] = 'Fits your age';
        }

        // Household income
        if ($listing->income_max !== null && $profile->income_monthly !== null) {
            if ($profile->income_monthly > $listing->income_max) {
                return null;
            }

            $score += 2;
            $reasons[] = 'Within the income limit';
        }

        // Nothing matched. Only keep it when it really is open to everyone.
        $openToAll = collect($listing->requirementRows())->except(['Location'])->isEmpty();

        if ($score === 0 && ! $openToAll) {
            return null;
        }

        if ($reasons === [] && $openToAll) {
            $reasons[] = 'Open to everyone';
        }

        $listing->setAttribute('match_score', $score);
        $listing->setAttribute('match_reasons', $reasons);

        return $listing;
    }
}

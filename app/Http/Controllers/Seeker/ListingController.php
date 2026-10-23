<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Support\Locations;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    private const SORTS = ['latest', 'deadline'];

    public function index(Request $request)
    {
        $types = config('napc.types');

        $type = array_key_exists((string) $request->query('type'), $types)
            ? $request->query('type')
            : 'all';

        $regions = Locations::regions();

        $region = array_key_exists(
            (string) $request->query('region'),
            $regions
        ) ? $request->query('region') : '';

        $sort = in_array(
            $request->query('sort'),
            self::SORTS,
            true
        ) ? $request->query('sort') : 'latest';

        $search = trim((string) $request->query('q', ''));

        $listings = Listing::open()
            ->with('user:id,organization')
            ->when(
                $type !== 'all',
                fn($q) => $q->where('type', $type)
            )
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . addcslashes($search, '%_\\') . '%';

                $q->where(fn($w) => $w
                    ->where('title', 'ilike', $like)
                    ->orWhere('description', 'ilike', $like));
            })
            // A region filter keeps nationwide listings too
            ->when(
                $region !== '',
                fn($q) => $q->where(fn($w) => $w
                    ->whereNull('region')
                    ->orWhere('region', $region))
            )
            ->when(
                $sort === 'deadline',
                fn($q) => $q->orderByRaw('deadline asc nulls last')
                    ->latest('published_at'),
                fn($q) => $q->latest('published_at')
            )
            ->paginate(9)
            ->withQueryString();

        $byType = Listing::open()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $counts = collect($types)
            ->map(fn($label, $key) => (int) ($byType[$key] ?? 0))
            ->all();

        $counts['all'] = array_sum($counts);

        return view(
            'seeker.listings.index',
            compact(
                'listings',
                'counts',
                'type',
                'region',
                'regions',
                'sort',
                'search'
            )
        );
    }

    public function show(Request $request, Listing $listing)
    {
        abort_unless($listing->status === 'published', 404);

        $listing->load('user:id,organization');

        $applied = $listing->applications()
            ->where('user_id', $request->user()->id)
            ->exists();

        return view(
            'seeker.listings.show',
            compact('listing', 'applied')
        );
    }
}

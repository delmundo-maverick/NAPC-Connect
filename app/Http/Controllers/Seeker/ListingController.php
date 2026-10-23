<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Support\Locations;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Province;

class ListingController extends Controller
{
    private const SORTS = ['latest', 'deadline'];

    public function index(Request $request)
    {
        $types = config('napc.types');

        $type = array_key_exists((string) $request->query('type'), $types) ? $request->query('type') : 'all';
        $sort = in_array($request->query('sort'), self::SORTS, true) ? $request->query('sort') : 'latest';
        $search = trim((string) $request->query('q', ''));

        // Location filter. Each level only counts if it belongs to the one above it.
        $regions = Locations::regions();
        $region = array_key_exists((string) $request->query('region'), $regions) ? (string) $request->query('region') : '';

        $province = $region !== '' && Province::where('code', (string) $request->query('province'))
            ->where('region_code', $region)->exists()
            ? (string) $request->query('province') : '';

        $city = $region !== '' && City::where('code', (string) $request->query('city'))
            ->where('region_code', $region)
            ->when($province !== '', fn($q) => $q->where(
                fn($w) => $w->where('province_code', $province)->orWhereNull('province_code')
            ))
            ->exists()
            ? (string) $request->query('city') : '';

        $area = match (true) {
            $city !== ''     => Locations::city($city),
            $province !== '' => Locations::province($province),
            $region !== ''   => $regions[$region],
            default          => null,
        };

        $listings = Listing::open()
            ->with('user:id,organization')
            ->when($type !== 'all', fn($q) => $q->where('type', $type))
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . addcslashes($search, '%_\\') . '%';

                $q->where(fn($w) => $w->where('title', 'ilike', $like)->orWhere('description', 'ilike', $like));
            })
            // Nationwide listings fit everyone. A narrower listing only shows if it covers the chosen place.
            ->when($region !== '', function ($q) use ($region, $province, $city) {
                $q->where(function ($w) use ($region, $province, $city) {
                    $w->whereNull('region')
                        ->orWhere(function ($in) use ($region, $province, $city) {
                            $in->where('region', $region)
                                ->when($province !== '', fn($p) => $p->where(
                                    fn($x) => $x->whereNull('province')->orWhere('province', $province)
                                ))
                                ->when($city !== '', fn($c) => $c->where(
                                    fn($x) => $x->whereNull('city')->orWhere('city', $city)
                                ));
                        });
                });
            })
            ->when(
                $sort === 'deadline',
                fn($q) => $q->orderByRaw('deadline asc nulls last')->latest('published_at'),
                fn($q) => $q->latest('published_at'),
            )
            ->paginate(9)
            ->withQueryString();

        $byType = Listing::open()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $counts = collect($types)->map(fn($label, $key) => (int) ($byType[$key] ?? 0))->all();
        $counts['all'] = array_sum($counts);

        return view('seeker.listings.index', compact(
            'listings',
            'counts',
            'type',
            'region',
            'province',
            'city',
            'area',
            'sort',
            'search'
        ));
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

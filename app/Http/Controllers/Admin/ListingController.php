<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    private const FILTERS = ['all', 'published', 'closed', 'removed'];

    public function index(Request $request)
    {
        $filter = in_array($request->query('status'), self::FILTERS, true) ? $request->query('status') : 'all';
        $type = array_key_exists((string) $request->query('type'), config('napc.types')) ? $request->query('type') : 'all';
        $search = trim((string) $request->query('q', ''));

        $listings = Listing::query()
            ->where('status', '!=', 'draft') // drafts are private to the organization
            ->with('user:id,organization,first_name,last_name,email')
            ->withCount('applications')
            ->when($filter === 'published', fn($q) => $q->where('status', 'published'))
            ->when($filter === 'closed', fn($q) => $q->where('status', 'closed')->whereNull('taken_down_at'))
            ->when($filter === 'removed', fn($q) => $q->whereNotNull('taken_down_at'))
            ->when($type !== 'all', fn($q) => $q->where('type', $type))
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . addcslashes($search, '%_\\') . '%';

                $q->where(fn($w) => $w
                    ->where('title', 'ilike', $like)
                    ->orWhereHas('user', fn($u) => $u->where('organization', 'ilike', $like)));
            })
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        $row = Listing::where('status', '!=', 'draft')
            ->selectRaw("
                count(*) as total,
                count(*) filter (where status = 'published') as published,
                count(*) filter (where status = 'closed' and taken_down_at is null) as closed,
                count(*) filter (where taken_down_at is not null) as removed
            ")
            ->first();

        $counts = [
            'all'       => (int) $row->total,
            'published' => (int) $row->published,
            'closed'    => (int) $row->closed,
            'removed'   => (int) $row->removed,
        ];

        return view('admin.listings.index', compact('listings', 'counts', 'filter', 'type', 'search'));
    }

    /** Close a listing and tell the organization why. */
    public function takeDown(Request $request, Listing $listing)
    {
        abort_if($listing->status === 'draft', 404);

        $data = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $listing->forceFill([
            'status'          => 'closed',
            'taken_down_at'   => now(),
            'takedown_reason' => trim($data['reason']),
        ])->save();

        return back()->with('status', "“{$listing->title}” was taken down.");
    }

    /** Lift the takedown. The organization decides when to reopen it. */
    public function restore(Request $request, Listing $listing)
    {
        if (! $listing->isTakenDown()) {
            return back();
        }

        $listing->forceFill([
            'taken_down_at'   => null,
            'takedown_reason' => null,
        ])->save();

        return back()->with('status', "“{$listing->title}” was restored. The organization can reopen it.");
    }
}

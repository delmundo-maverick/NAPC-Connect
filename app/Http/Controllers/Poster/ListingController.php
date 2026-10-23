<?php

namespace App\Http\Controllers\Poster;

use App\Http\Controllers\Controller;
use App\Http\Requests\Poster\ListingRequest;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    private const STATUSES = ['draft', 'published', 'closed', 'all'];

    public function index(Request $request)
    {
        $user = $request->user();

        $status = in_array($request->query('status'), self::STATUSES, true) ? $request->query('status') : 'all';
        $search = trim((string) $request->query('q', ''));

        $listings = $user->listings()
            ->withCount('applications')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->when($search !== '', fn($q) => $q->where('title', 'ilike', '%' . addcslashes($search, '%_\\') . '%'))
            ->latest('updated_at')
            ->paginate(8)
            ->withQueryString();

        $byStatus = $user->listings()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'draft'     => (int) ($byStatus['draft'] ?? 0),
            'published' => (int) ($byStatus['published'] ?? 0),
            'closed'    => (int) ($byStatus['closed'] ?? 0),
        ];
        $counts['all'] = array_sum($counts);

        return view('poster.listings.index', compact('listings', 'status', 'search', 'counts'));
    }

    public function create(Request $request)
    {
        $type = array_key_exists($request->query('type'), config('napc.types')) ? $request->query('type') : 'scholarship';

        return view('poster.listings.create', [
            'listing' => new Listing(['type' => $type, 'apply_mode' => 'info']),
        ]);
    }

    public function store(ListingRequest $request)
    {
        $listing = $request->user()->listings()->create(
            $request->payload() + $this->statusFields($request, null)
        );

        return redirect()
            ->route('poster.listings.index')
            ->with('status', $listing->status === 'published'
                ? "“{$listing->title}” is now live."
                : "“{$listing->title}” was saved as a draft.");
    }

    public function edit(Request $request, Listing $listing)
    {
        $this->owned($request, $listing);

        return view('poster.listings.edit', compact('listing'));
    }

    public function update(ListingRequest $request, Listing $listing)
    {
        $this->owned($request, $listing);

        if ($listing->isTakenDown() && $request->input('action') === 'publish') {
            return back()->withInput()->with('warning', 'An admin took this listing down. You can edit it, but it cannot be published until the admin restores it.');
        }

        $listing->update($request->payload() + $this->statusFields($request, $listing));

        return redirect()
            ->route('poster.listings.index')
            ->with('status', "“{$listing->title}” was updated.");
    }

    /** Publish, close, reopen or move to draft */
    public function status(Request $request, Listing $listing)
    {
        $this->owned($request, $listing);

        $data = $request->validate([
            'status' => ['required', Rule::in(['draft', 'published', 'closed'])],
        ]);

        if ($data['status'] === 'published' && $listing->isTakenDown()) {
            return back()->with('warning', 'An admin took this listing down. It cannot be published until the admin restores it.');
        }

        if ($data['status'] === 'published' && $listing->isExpired()) {
            return back()->with('warning', 'The deadline has passed. Edit the listing and set a new deadline first.');
        }

        $listing->update([
            'status'       => $data['status'],
            'published_at' => $data['status'] === 'published' ? ($listing->published_at ?? now()) : $listing->published_at,
        ]);

        return back()->with('status', match ($data['status']) {
            'published' => "“{$listing->title}” is now live.",
            'closed'    => "“{$listing->title}” was closed.",
            default     => "“{$listing->title}” was moved to drafts.",
        });
    }

    public function destroy(Request $request, Listing $listing)
    {
        $this->owned($request, $listing);

        $title = $listing->title;
        $listing->delete();

        return redirect()->route('poster.listings.index')->with('status', "“{$title}” was deleted.");
    }

    /** Organizations can only touch their own listings. */
    private function owned(Request $request, Listing $listing): void
    {
        abort_unless((int) $listing->user_id === (int) $request->user()->id, 404);
    }

    private function statusFields(Request $request, ?Listing $listing): array
    {
        if ($request->input('action') === 'publish') {
            return ['status' => 'published', 'published_at' => $listing?->published_at ?? now()];
        }

        // "Save" keeps the current status. A brand new listing starts as a draft.
        return $listing ? [] : ['status' => 'draft'];
    }
}

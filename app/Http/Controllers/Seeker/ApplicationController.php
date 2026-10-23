<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seeker\ApplicationRequest;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /** My applications */
    public function index(Request $request)
    {
        $applications = $request->user()
            ->applications()
            ->with('listing.user:id,organization')
            ->latest()
            ->paginate(10);

        return view('seeker.applications.index', compact('applications'));
    }

    /** The application form */
    public function create(Request $request, Listing $listing)
    {
        if ($blocked = $this->blocked($request, $listing)) {
            return $blocked;
        }

        $listing->load('user:id,organization');

        return view('seeker.applications.create', compact('listing'));
    }

    /** Save the application */
    public function store(ApplicationRequest $request, Listing $listing)
    {
        if ($blocked = $this->blocked($request, $listing)) {
            return $blocked;
        }

        // firstOrCreate stops a double click from sending two applications
        $application = $listing->applications()->firstOrCreate(
            ['user_id' => $request->user()->id],
            ['answers' => $request->answers(), 'status' => 'submitted'],
        );

        if (! $application->wasRecentlyCreated) {
            return redirect()
                ->route('seeker.applications.index')
                ->with('warning', 'You already applied to this listing.');
        }

        return redirect()
            ->route('seeker.applications.index')
            ->with('status', "Your application for “{$listing->title}” was sent.");
    }

    /** Only open listings that use an NAPC Connect form, once per seeker. */
    private function blocked(Request $request, Listing $listing): ?RedirectResponse
    {
        abort_unless($listing->status === 'published', 404);

        $back = redirect()->route('seeker.listings.show', $listing);

        if ($listing->apply_mode !== 'form') {
            return $back->with('warning', 'This listing does not use an NAPC Connect form.');
        }

        if ($listing->isExpired()) {
            return $back->with('warning', 'The deadline for this listing has passed.');
        }

        if ($listing->applications()->where('user_id', $request->user()->id)->exists()) {
            return $back->with('warning', 'You already applied to this listing.');
        }

        return null;
    }
}

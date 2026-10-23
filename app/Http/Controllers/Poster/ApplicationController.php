<?php

namespace App\Http\Controllers\Poster;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request, Listing $listing)
    {
        abort_unless((int) $listing->user_id === (int) $request->user()->id, 404);

        $applications = $listing->applications()
            ->with('user.seekerProfile')
            ->latest()
            ->paginate(15);

        return view('poster.listings.applications', compact('listing', 'applications'));
    }
}

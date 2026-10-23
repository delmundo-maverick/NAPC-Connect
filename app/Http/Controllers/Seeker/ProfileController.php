<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seeker\ProfileRequest;
use App\Models\SeekerProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('seeker.profile.edit', [
            'profile' => $request->user()->seekerProfile ?? new SeekerProfile(),
        ]);
    }

    public function update(ProfileRequest $request)
    {
        $request->user()->seekerProfile()->updateOrCreate([], $request->payload());

        return redirect()
            ->route('seeker.home')
            ->with('status', 'Your profile was saved.');
    }
}

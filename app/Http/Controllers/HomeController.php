<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RecommendationService;

class HomeController extends Controller
{
    /** /dashboard sends each role to its own home page */
    public function dashboard(Request $request)
    {
        return redirect()->route($request->user()->homeRoute());
    }

    public function seeker(Request $request, RecommendationService $recommendations)
    {
        $user = $request->user();

        return view('seeker.home', [
            'completion'  => $user->seekerProfile?->completion() ?? 0,
            'recommended' => $recommendations->forUser($user),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Poster;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $byStatus = $user->listings()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'published'    => (int) ($byStatus['published'] ?? 0),
            'draft'        => (int) ($byStatus['draft'] ?? 0),
            'closed'       => (int) ($byStatus['closed'] ?? 0),
            'applications' => Application::whereHas('listing', fn($q) => $q->where('user_id', $user->id))->count(),
        ];

        $recent = $user->listings()
            ->withCount('applications')
            ->latest('updated_at')
            ->limit(5)
            ->get();

        return view('poster.home', compact('user', 'counts', 'recent'));
    }
}

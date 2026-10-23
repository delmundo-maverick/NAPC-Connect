<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // One grouped query instead of several counts
        $rows = User::query()
            ->selectRaw('role, poster_status, count(*) as total')
            ->groupBy('role', 'poster_status')
            ->get();

        $count = fn (string $role, ?string $status = null) => (int) $rows
            ->where('role', $role)
            ->when($status, fn ($items) => $items->where('poster_status', $status))
            ->sum('total');

        return view('admin.home', [
            'counts' => [
                'seekers'  => $count('seeker'),
                'approved' => $count('poster', 'approved'),
                'pending'  => $count('poster', 'pending'),
                'rejected' => $count('poster', 'rejected'),
            ],
            // The oldest requests first, capped so the dashboard stays short
            'pending' => User::where('role', 'poster')
                ->where('poster_status', 'pending')
                ->oldest()
                ->limit(5)
                ->get(),
            'latest' => User::latest()->limit(5)->get(),
        ]);
    }
}

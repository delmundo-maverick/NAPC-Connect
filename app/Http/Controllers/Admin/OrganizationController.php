<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    private const STATUSES = ['pending', 'approved', 'rejected', 'all'];

    public function index(Request $request)
    {
        $status = in_array($request->query('status'), self::STATUSES, true)
            ? $request->query('status')
            : 'pending';

        $search = trim((string) $request->query('q', ''));

        $orgs = User::query()
            ->where('role', 'poster')
            ->when($status !== 'all', fn ($q) => $q->where('poster_status', $status))
            ->when($search !== '', function ($q) use ($search) {
                // Escape % and _ so they are searched literally
                $like = '%' . addcslashes($search, '%_\\') . '%';

                $q->where(fn ($w) => $w
                    ->where('organization', 'ilike', $like)
                    ->orWhere('first_name', 'ilike', $like)
                    ->orWhere('last_name', 'ilike', $like)
                    ->orWhere('email', 'ilike', $like));
            })
            // Pending: oldest request first. Everything else: newest first.
            ->when($status === 'pending', fn ($q) => $q->oldest(), fn ($q) => $q->latest())
            ->paginate(10)
            ->withQueryString();

        $byStatus = User::where('role', 'poster')
            ->selectRaw('poster_status, count(*) as total')
            ->groupBy('poster_status')
            ->pluck('total', 'poster_status');

        $counts = [
            'pending'  => (int) ($byStatus['pending'] ?? 0),
            'approved' => (int) ($byStatus['approved'] ?? 0),
            'rejected' => (int) ($byStatus['rejected'] ?? 0),
        ];
        $counts['all'] = array_sum($counts);

        return view('admin.organizations.index', compact('orgs', 'status', 'search', 'counts'));
    }
}

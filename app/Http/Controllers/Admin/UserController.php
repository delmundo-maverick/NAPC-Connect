<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private const ROLES = ['seeker', 'poster', 'admin'];

    public function index(Request $request)
    {
        $role = in_array($request->query('role'), self::ROLES, true)
            ? $request->query('role')
            : 'all';

        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($role !== 'all', fn ($q) => $q->where('role', $role))
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . addcslashes($search, '%_\\') . '%';

                $q->where(fn ($w) => $w
                    ->where('first_name', 'ilike', $like)
                    ->orWhere('last_name', 'ilike', $like)
                    ->orWhere('email', 'ilike', $like)
                    ->orWhere('organization', 'ilike', $like));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $byRole = User::query()
            ->selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $counts = [
            'seeker' => (int) ($byRole['seeker'] ?? 0),
            'poster' => (int) ($byRole['poster'] ?? 0),
            'admin'  => (int) ($byRole['admin'] ?? 0),
        ];
        $counts['all'] = array_sum($counts);

        return view('admin.users.index', compact('users', 'role', 'search', 'counts'));
    }
}

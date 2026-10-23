<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class PosterApprovalController extends Controller
{
    public function approve(User $user)
    {
        return $this->setStatus($user, 'approved');
    }

    public function reject(User $user)
    {
        return $this->setStatus($user, 'rejected');
    }

    private function setStatus(User $user, string $status)
    {
        abort_unless($user->role === 'poster', 404);

        $user->update(['poster_status' => $status]);

        return back()->with('status', "{$user->organization} was {$status}.");
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedPoster
{
    /** Organizations can post only after an admin approves them. */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'poster' && $user->poster_status !== 'approved') {
            return redirect()
                ->route('poster.home')
                ->with('warning', 'Your organization must be approved before you can post listings.');
        }

        return $next($request);
    }
}

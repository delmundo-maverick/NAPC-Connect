<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        // /register?role=poster pre-selects the poster option (used by the landing page button)
        $role = $request->query('role') === 'poster' ? 'poster' : 'seeker';

        return view('auth.register', ['role' => $role]);
    }

    public function store(Request $request)
    {
        // Letters (including ñ and accents), spaces, dots, hyphens and apostrophes
        $nameRule = "regex:/^[\pL\s.'-]+$/u";

        $data = $request->validate([
            'role'              => ['required', Rule::in(['seeker', 'poster'])], // admin can never be chosen here
            'first_name'        => ['required', 'string', 'max:60', $nameRule],
            'middle_name'       => ['nullable', 'string', 'max:60', $nameRule],
            'last_name'         => ['required', 'string', 'max:60', $nameRule],
            'email'             => ['required', 'email', 'lowercase', 'max:255', 'unique:users,email'],
            'password'          => ['required', 'confirmed', Password::min(8)],

            'organization'      => ['required_if:role,poster', 'nullable', 'string', 'max:150'],
            'organization_type' => ['required_if:role,poster', 'nullable', Rule::in(['school', 'lgu', 'agency', 'company', 'ngo'])],
            'position'          => ['required_if:role,poster', 'nullable', 'string', 'max:120'],
            'proof_url'         => ['required_if:role,poster', 'nullable', 'url', 'max:500'],
        ]);

        $isPoster = $data['role'] === 'poster';

        $user = User::create([
            'first_name'        => trim($data['first_name']),
            'middle_name'       => filled($data['middle_name'] ?? null) ? trim($data['middle_name']) : null,
            'last_name'         => trim($data['last_name']),
            'email'             => $data['email'],
            'password'          => $data['password'],
            'role'              => $data['role'],
            'poster_status'     => $isPoster ? 'pending' : null,
            'organization'      => $isPoster ? $data['organization'] : null,
            'organization_type' => $isPoster ? $data['organization_type'] : null,
            'position'          => $isPoster ? $data['position'] : null,
            'proof_url'         => $isPoster ? $data['proof_url'] : null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->homeRoute())->with(
            'status',
            $isPoster
                ? 'Account created. Your organization is waiting for admin review. You can post once it is approved.'
                : 'Welcome to NAPC Connect! Your account is ready.'
        );
    }
}

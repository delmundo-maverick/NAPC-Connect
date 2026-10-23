<?php

use Illuminate\Support\Facades\Route;

// Shared
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;

// Auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Seeker
use App\Http\Controllers\Seeker\ListingController as SeekerListingController;
use App\Http\Controllers\Seeker\ProfileController;
use App\Http\Controllers\Seeker\ApplicationController as SeekerApplicationController;

// Organization / Poster
use App\Http\Controllers\Poster\ApplicationController;
use App\Http\Controllers\Poster\DashboardController as PosterDashboardController;
use App\Http\Controllers\Poster\ListingController;

// Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\PosterApprovalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ListingController as AdminListingController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
| Anyone can open these pages, logged in or not.
*/

// Landing page
Route::view('/', 'home')->name('home');

/*
|--------------------------------------------------------------------------
| Guests only
|--------------------------------------------------------------------------
| Logged-in users are redirected away from these pages.
*/
Route::middleware('guest')->group(function () {

    // Register
    Route::get('/register', [RegisterController::class, 'create'])
        ->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Login
    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Logged-in users
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // ---------------------------------------------------------------
    // Shared by every role
    // ---------------------------------------------------------------

    // Logout
    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

    // Sends each role to its own home page
    Route::get('/dashboard', [HomeController::class, 'dashboard'])
        ->name('dashboard');

    // Location dropdown data. Regions are on the page; provinces and cities load as needed.
    Route::get('/locations/provinces', [LocationController::class, 'provinces'])
        ->name('locations.provinces');
    Route::get('/locations/cities', [LocationController::class, 'cities'])
        ->name('locations.cities');

    // ---------------------------------------------------------------
    // Seeker
    // Route names start with "seeker."
    // ---------------------------------------------------------------
    Route::middleware('role:seeker')
        ->name('seeker.')
        ->group(function () {

            // Home (/home) -> seeker.home
            Route::get('/home', [HomeController::class, 'seeker'])
                ->name('home');

            // Profile -> seeker.profile.edit, seeker.profile.update
            Route::get('/profile', [ProfileController::class, 'edit'])
                ->name('profile.edit');
            Route::put('/profile', [ProfileController::class, 'update'])
                ->name('profile.update');

            // Browse and view listings -> seeker.listings.index, seeker.listings.show
            Route::get('/listings', [SeekerListingController::class, 'index'])
                ->name('listings.index');
            Route::get('/listings/{listing}', [SeekerListingController::class, 'show'])
                ->name('listings.show');

            // Apply to a listing -> seeker.applications.create, seeker.applications.store
            Route::get('/listings/{listing}/apply', [SeekerApplicationController::class, 'create'])
                ->name('applications.create');
            Route::post('/listings/{listing}/apply', [SeekerApplicationController::class, 'store'])
                ->name('applications.store');

            // My applications -> seeker.applications.index
            Route::get('/applications', [SeekerApplicationController::class, 'index'])
                ->name('applications.index');
        });

    // ---------------------------------------------------------------
    // Organization / Poster
    // URLs start with /organization, route names start with "poster."
    // ---------------------------------------------------------------
    Route::middleware('role:poster')
        ->prefix('organization')
        ->name('poster.')
        ->group(function () {

            // Home -> poster.home
            Route::get('/', [PosterDashboardController::class, 'index'])
                ->name('home');

            // Only approved organizations can manage listings
            Route::middleware('poster.approved')->group(function () {

                // Listings: list, create, edit, delete
                Route::get('/listings', [ListingController::class, 'index'])
                    ->name('listings.index');
                Route::get('/listings/create', [ListingController::class, 'create'])
                    ->name('listings.create');
                Route::post('/listings', [ListingController::class, 'store'])
                    ->name('listings.store');
                Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])
                    ->name('listings.edit');
                Route::put('/listings/{listing}', [ListingController::class, 'update'])
                    ->name('listings.update');
                Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])
                    ->name('listings.destroy');

                // Publish, close, reopen or move a listing to drafts
                Route::patch('/listings/{listing}/status', [ListingController::class, 'status'])
                    ->name('listings.status');

                // Applicants of a listing
                Route::get(
                    '/listings/{listing}/applications',
                    [ApplicationController::class, 'index']
                )->name('listings.applications');
            });
        });

    // ---------------------------------------------------------------
    // Admin
    // URLs start with /admin, route names start with "admin."
    // ---------------------------------------------------------------
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Dashboard -> admin.home
            Route::get('/', [DashboardController::class, 'index'])
                ->name('home');

            // Organizations and users
            Route::get('/organizations', [OrganizationController::class, 'index'])
                ->name('organizations.index');
            Route::get('/users', [UserController::class, 'index'])
                ->name('users.index');

            // Approve or reject an organization
            Route::post('/posters/{user}/approve', [PosterApprovalController::class, 'approve'])
                ->name('posters.approve');
            Route::post('/posters/{user}/reject', [PosterApprovalController::class, 'reject'])
                ->name('posters.reject');

            // Listing moderation -> admin.listings.index, admin.listings.takedown, admin.listings.restore
            Route::get('/listings', [AdminListingController::class, 'index'])
                ->name('listings.index');
            Route::post('/listings/{listing}/takedown', [AdminListingController::class, 'takeDown'])
                ->name('listings.takedown');
            Route::post('/listings/{listing}/restore', [AdminListingController::class, 'restore'])
                ->name('listings.restore');
        });
});

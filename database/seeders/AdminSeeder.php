<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Safe to run many times: it updates the same admin instead of creating duplicates.
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@napcconnect.test')],
            [
                'first_name' => 'NAPC',
                'middle_name' => null,
                'last_name' => 'Admin',
                'password' => env('ADMIN_PASSWORD', 'ChangeMe123!'), // hashed by the User model cast
                'role' => 'admin',
                'poster_status' => null,
            ]
        );
    }
}

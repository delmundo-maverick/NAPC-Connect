<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name', 60)->after('id');
            }
            if (!Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name', 60)->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name', 60)->after('middle_name');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('seeker')->after('password'); // seeker | poster | admin
            }
            if (!Schema::hasColumn('users', 'poster_status')) {
                $table->string('poster_status')->nullable()->after('role'); // pending | approved | rejected
            }
            if (!Schema::hasColumn('users', 'organization')) {
                $table->string('organization')->nullable();
            }
            if (!Schema::hasColumn('users', 'organization_type')) {
                $table->string('organization_type')->nullable(); // school | lgu | agency | company | ngo
            }
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable();
            }
            if (!Schema::hasColumn('users', 'proof_url')) {
                $table->string('proof_url', 500)->nullable();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = array_filter([
                'first_name',
                'middle_name',
                'last_name',
                'role',
                'poster_status',
                'organization',
                'organization_type',
                'position',
                'proof_url',
            ], fn($col) => Schema::hasColumn('users', $col));

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Old values were hard-coded keys and free text, so they cannot be converted.
        DB::table('listings')->update(['region' => null, 'province' => null, 'city' => null]);
        DB::table('seeker_profiles')->update(['region' => null, 'province' => null, 'city' => null]);

        foreach (['listings', 'seeker_profiles'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->string('region', 10)->nullable()->change();
                $table->string('province', 10)->nullable()->change();
                $table->string('city', 10)->nullable()->change();
                $table->index('region');
            });
        }
    }

    public function down(): void
    {
        foreach (['listings', 'seeker_profiles'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropIndex(['region']);
                $table->string('region', 20)->nullable()->change();
                $table->string('province', 80)->nullable()->change();
                $table->string('city', 100)->nullable()->change();
            });
        }
    }
};

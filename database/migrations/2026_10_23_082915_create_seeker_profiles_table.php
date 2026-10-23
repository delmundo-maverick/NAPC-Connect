<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seeker_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->date('birthdate')->nullable();

            // PSGC codes (see the psgc_regions, psgc_provinces and psgc_cities tables)
            $table->string('region', 20)->nullable();
            $table->string('province', 80)->nullable();
            $table->string('city', 100)->nullable();

            // Same keys as config/napc.php. year_level "none" means not in school.
            $table->string('year_level', 20)->nullable();
            $table->string('school_type', 20)->nullable();
            $table->string('school_name', 150)->nullable();
            $table->string('course', 100)->nullable();

            $table->jsonb('sectors')->nullable();
            $table->unsignedInteger('income_monthly')->nullable(); // household, PHP

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seeker_profiles');
    }
};

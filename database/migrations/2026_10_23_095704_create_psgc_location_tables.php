<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psgc_regions', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name', 150);
            $table->string('short_name', 50)->nullable();   // e.g. "Region IV-A"
        });

        Schema::create('psgc_provinces', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('region_code', 10)->index();
            $table->string('name', 150);
        });

        Schema::create('psgc_cities', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('region_code', 10);
            $table->string('province_code', 10)->nullable();  // null for NCR and independent cities
            $table->string('name', 150);
            $table->boolean('is_city')->default(false);

            $table->index(['region_code', 'province_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psgc_cities');
        Schema::dropIfExists('psgc_provinces');
        Schema::dropIfExists('psgc_regions');
    }
};

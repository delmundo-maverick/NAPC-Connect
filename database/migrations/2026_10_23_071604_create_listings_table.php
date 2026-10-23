<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Basics
            $table->string('type', 20);                      // ayuda | scholarship | job | training
            $table->string('title', 200);
            $table->text('description');
            $table->string('status', 20)->default('draft');  // draft | published | closed
            $table->timestamp('published_at')->nullable();
            $table->date('deadline')->nullable();
            $table->string('source_url', 500)->nullable();

            // Who can apply (all optional: empty means open to everyone)
            $table->string('region', 20)->nullable();
            $table->string('province', 80)->nullable();
            $table->string('city', 100)->nullable();
            $table->jsonb('year_levels')->nullable();
            $table->jsonb('school_types')->nullable();
            $table->jsonb('courses')->nullable();
            $table->jsonb('sectors')->nullable();
            $table->unsignedSmallInteger('age_min')->nullable();
            $table->unsignedSmallInteger('age_max')->nullable();
            $table->unsignedInteger('income_max')->nullable();  // monthly household income, PHP
            $table->text('other_requirements')->nullable();

            // Optional extra details the organization picked (salary, benefits, slots...)
            $table->jsonb('details')->nullable();

            // How to apply
            $table->string('apply_mode', 10)->default('info');  // form | link | info
            $table->string('apply_url', 500)->nullable();
            $table->text('how_to_apply')->nullable();
            $table->jsonb('application_form')->nullable();      // questions built by the organization

            $table->timestamps();

            $table->index(['status', 'type']);
            $table->index('deadline');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};

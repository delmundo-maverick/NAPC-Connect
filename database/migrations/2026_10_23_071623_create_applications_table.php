<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->jsonb('answers')->nullable();                // { question_key: answer }
            $table->string('status', 20)->default('submitted');
            $table->timestamps();

            $table->unique(['listing_id', 'user_id']);           // one application per seeker
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

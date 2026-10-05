<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artist_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('artist_name', 100);
            $table->string('tagline', 150)->nullable();
            $table->string('location', 100)->nullable();
            $table->text('short_bio')->nullable();
            $table->text('biography')->nullable();
            $table->string('award', 200)->nullable();
            $table->string('booking_email');
            $table->json('social_links')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_profiles');
    }
};
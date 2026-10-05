<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('alt_text', 255);
            $table->string('image_path');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->string('placement', 20)->default('gallery');
            $table->string('credit', 150)->nullable();
            $table->boolean('in_press_kit')->default(false);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['placement', 'is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
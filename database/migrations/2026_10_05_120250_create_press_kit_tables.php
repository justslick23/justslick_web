<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Single-row table, read through PressKit::current().
        Schema::create('press_kits', function (Blueprint $table) {
            $table->id();
            $table->string('real_name', 120)->nullable();
            $table->string('nickname', 120)->nullable();
            $table->unsignedSmallInteger('active_since')->nullable();
            $table->string('genres', 255)->nullable();
            $table->text('awards')->nullable();
            $table->text('influences')->nullable();
            $table->text('collaborators')->nullable();
            $table->timestamps();
        });

        Schema::create('press_assets', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('kind', 20)->default('other');
            $table->string('file_path');
            $table->string('original_name');
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('press_assets');
        Schema::dropIfExists('press_kits');
    }
};
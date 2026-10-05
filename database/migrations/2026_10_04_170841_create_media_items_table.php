<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('release_id')
                ->nullable()
                ->constrained('releases')
                ->nullOnDelete();

            $table->string('title', 150);
            $table->string('type', 30);
            $table->string('source_url', 500);
            $table->text('description')->nullable();
            $table->date('published_on')->nullable();

            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_items');
    }
};
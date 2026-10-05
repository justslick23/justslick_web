<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 150);
            $table->string('phone', 50)->nullable();
            $table->string('booking_type', 30);
            $table->date('event_date')->nullable();
            $table->string('budget', 30)->nullable();
            $table->string('subject', 200)->nullable();
            $table->text('message');

            $table->string('status', 20)->default('new');
            $table->string('notification_status', 20)->default('pending');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_enquiries');
    }
};
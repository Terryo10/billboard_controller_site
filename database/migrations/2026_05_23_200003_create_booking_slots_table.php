<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('slot_template_id')->constrained('time_slot_templates')->cascadeOnDelete();
            $table->date('air_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            // Prevent double-booking: same station + slot template + air date
            $table->unique(['slot_template_id', 'air_date'], 'unique_slot_booking');
            $table->index(['booking_id']);
            $table->index(['air_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_slots');
    }
};

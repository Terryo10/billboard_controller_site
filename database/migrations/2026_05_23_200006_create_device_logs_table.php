<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->string('event_type'); // heartbeat, error, playback_start, etc.
            $table->json('payload')->nullable();
            $table->timestamp('logged_at')->useCurrent();

            $table->index(['station_id', 'event_type']);
            $table->index('logged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_logs');
    }
};

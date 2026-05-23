<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location_name');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('photo')->nullable();
            $table->text('description')->nullable();
            $table->integer('screen_width')->nullable()->comment('pixels');
            $table->integer('screen_height')->nullable()->comment('pixels');
            $table->string('screen_size')->nullable()->comment('e.g. 55 inch');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('device_token', 64)->unique()->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};

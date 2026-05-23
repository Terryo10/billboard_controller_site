<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->enum('file_type', ['image', 'video'])->nullable();
            $table->string('original_filename')->nullable();
            $table->bigInteger('file_size')->nullable()->comment('bytes');
            $table->integer('duration_seconds')->nullable();
            $table->string('checksum', 64)->nullable()->comment('SHA-256');
            $table->enum('status', ['pending_upload', 'pending_review', 'approved', 'rejected'])->default('pending_upload');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adverts');
    }
};

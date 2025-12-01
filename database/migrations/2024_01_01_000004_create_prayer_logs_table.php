<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prayer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Prayer details
            $table->date('prayer_date');
            $table->enum('prayer_name', ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha']);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            
            // Prayer quality tracking
            $table->boolean('on_time')->default(false);
            $table->boolean('in_congregation')->default(false);
            $table->boolean('at_mosque')->default(false);
            
            // Points calculation
            $table->integer('points_earned')->default(0);
            
            $table->timestamps();
            
            $table->unique(['user_id', 'prayer_date', 'prayer_name']);
            $table->index('user_id');
            $table->index('prayer_date');
            $table->index(['user_id', 'prayer_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayer_logs');
    }
};

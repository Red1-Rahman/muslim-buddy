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
        Schema::create('user_verse_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('verse_id')->constrained()->onDelete('cascade');
            
            // Progress tracking
            $table->boolean('is_read')->default(false);
            $table->boolean('is_understood')->default(false);
            $table->boolean('is_memorized')->default(false);
            
            // Timestamps for each achievement
            $table->timestamp('read_at')->nullable();
            $table->timestamp('understood_at')->nullable();
            $table->timestamp('memorized_at')->nullable();
            
            // Review tracking for memorization
            $table->integer('review_count')->default(0);
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamp('next_review_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['user_id', 'verse_id']);
            $table->index('user_id');
            $table->index(['user_id', 'is_memorized']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_verse_progress');
    }
};

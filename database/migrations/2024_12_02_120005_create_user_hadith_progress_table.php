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
        Schema::create('user_hadith_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('hadith_id')->constrained('hadiths')->onDelete('cascade');
            $table->enum('status', ['not_read', 'read', 'memorized']);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('memorized_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Prevent duplicate entries
            $table->unique(['user_id', 'hadith_id']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_hadith_progress');
    }
};
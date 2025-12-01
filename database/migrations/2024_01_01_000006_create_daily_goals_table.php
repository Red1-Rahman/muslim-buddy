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
        Schema::create('daily_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->date('goal_date');
            $table->integer('target_verses')->default(5); // Daily verse reading goal
            $table->integer('verses_completed')->default(0);
            $table->boolean('all_prayers_completed')->default(false);
            
            $table->timestamps();
            
            $table->unique(['user_id', 'goal_date']);
            $table->index('user_id');
            $table->index('goal_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_goals');
    }
};

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            
            // User profile fields
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable();
            $table->string('calculation_method')->default('MuslimWorldLeague');
            $table->string('madhab')->default('Shafi'); // Shafi or Hanafi
            $table->string('timezone')->default('UTC');
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            
            // Gamification
            $table->integer('total_points')->default(0);
            $table->integer('prayer_streak')->default(0);
            $table->date('last_prayer_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

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
        Schema::create('surahs', function (Blueprint $table) {
            $table->id();
            $table->integer('surah_number')->unique(); // 1-114
            $table->string('name_arabic');
            $table->string('name_english');
            $table->string('name_transliteration');
            $table->integer('total_verses');
            $table->string('revelation_type'); // Meccan or Medinan
            $table->integer('revelation_order')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('surah_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surahs');
    }
};

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
        Schema::create('verses', function (Blueprint $table) {
            $table->id();
            $table->integer('surah_number'); // 1-114
            $table->integer('verse_number');
            $table->text('arabic_text');
            $table->text('transliteration')->nullable();
            $table->text('translation_english')->nullable();
            $table->text('translation_bengali')->nullable();
            $table->integer('juz')->nullable(); // Para number 1-30
            $table->integer('page')->nullable(); // Mushaf page number
            $table->string('revelation_type')->nullable(); // Meccan or Medinan
            $table->timestamps();
            
            $table->unique(['surah_number', 'verse_number']);
            $table->index('surah_number');
            $table->index('juz');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verses');
    }
};

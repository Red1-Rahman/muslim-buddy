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
        Schema::create('hadith_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('hadith_chapters')->onDelete('cascade');
            $table->decimal('section_number', 8, 1);
            $table->text('section_name_english')->nullable();
            $table->text('section_name_arabic')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hadith_sections');
    }
};
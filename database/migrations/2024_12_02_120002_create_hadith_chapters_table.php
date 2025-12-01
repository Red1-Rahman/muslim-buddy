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
        Schema::create('hadith_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('hadith_collections')->onDelete('cascade');
            $table->decimal('chapter_number', 8, 1);
            $table->string('chapter_name_english');
            $table->string('chapter_name_arabic');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hadith_chapters');
    }
};
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
        Schema::create('hadiths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('hadith_collections')->onDelete('cascade');
            $table->foreignId('chapter_id')->constrained('hadith_chapters')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('hadith_sections')->onDelete('cascade');
            $table->decimal('hadith_number', 8, 1);
            $table->longText('english_hadith');
            $table->text('english_isnad');
            $table->longText('english_matn');
            $table->longText('arabic_hadith');
            $table->text('arabic_isnad');
            $table->longText('arabic_matn');
            $table->text('arabic_comment')->nullable();
            $table->string('english_grade');
            $table->string('arabic_grade');
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['collection_id', 'chapter_id']);
            $table->index('hadith_number');
            $table->index('english_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hadiths');
    }
};
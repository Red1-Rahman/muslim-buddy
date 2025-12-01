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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('prayer_notifications')->default(true)->after('timezone');
            $table->integer('reminder_minutes')->nullable()->after('prayer_notifications');
            $table->string('quran_translation', 20)->default('both')->after('reminder_minutes');
            $table->string('arabic_text_size', 10)->default('medium')->after('quran_translation');
            $table->integer('daily_verse_goal')->default(5)->after('arabic_text_size');
            $table->boolean('enable_night_mode')->default(false)->after('daily_verse_goal');
            $table->boolean('auto_mark_prayers')->default(false)->after('enable_night_mode');
            $table->boolean('congregation_points_bonus')->default(true)->after('auto_mark_prayers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'prayer_notifications',
                'reminder_minutes',
                'quran_translation',
                'arabic_text_size',
                'daily_verse_goal',
                'enable_night_mode',
                'auto_mark_prayers',
                'congregation_points_bonus'
            ]);
        });
    }
};
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
        if (!Schema::hasColumn('surahs', 'description')) {
            Schema::table('surahs', function (Blueprint $table) {
                $table->text('description')->nullable()->after('revelation_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('surahs', 'description')) {
            Schema::table('surahs', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};

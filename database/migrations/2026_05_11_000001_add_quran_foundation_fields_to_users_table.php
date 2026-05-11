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
            $table->string('qf_user_id')->nullable()->unique()->after('google_id');
            $table->string('qf_email')->nullable()->after('qf_user_id');
            $table->timestamp('qf_profile_synced_at')->nullable()->after('qf_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['qf_user_id']);
            $table->dropColumn(['qf_user_id', 'qf_email', 'qf_profile_synced_at']);
        });
    }
};

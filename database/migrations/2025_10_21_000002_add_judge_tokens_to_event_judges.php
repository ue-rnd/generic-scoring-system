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
        Schema::table('event_judges', function (Blueprint $table) {
            // Make judge_id nullable (for token-based judges without accounts)
            $table->foreignId('judge_id')->nullable()->change();
            
            // Drop the unique constraint (we'll allow multiple invites per judge now)
            $table->dropUnique(['event_id', 'judge_id']);
            
            // Unique token for each judge-event combination
            $table->string('judge_token', 64)->unique()->nullable()->after('judge_id');
            
            // Judge's display name (can be different from their account name)
            $table->string('judge_name')->nullable()->after('judge_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_judges', function (Blueprint $table) {
            // Drop the unique index first (SQLite requirement)
            $table->dropUnique(['judge_token']);
            $table->dropColumn(['judge_token', 'judge_name']);
            
            // Restore the unique constraint and make judge_id required
            $table->foreignId('judge_id')->nullable(false)->change();
            $table->unique(['event_id', 'judge_id']);
        });
    }
};

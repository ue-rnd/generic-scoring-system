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
        Schema::table('scores', function (Blueprint $table) {
            // Add event_judge_id for token-based scoring
            $table->foreignId('event_judge_id')->nullable()->after('judge_id')->constrained('event_judges')->nullOnDelete();
            
            // Make judge_id nullable (scores can come from either authenticated judges OR token-based event_judges)
            $table->foreignId('judge_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropForeign(['event_judge_id']);
            $table->dropColumn('event_judge_id');
            
            // Note: Cannot easily revert judge_id to non-nullable
        });
    }
};

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
            // Add question_number for quiz bee scoring
            $table->unsignedInteger('question_number')->nullable()->after('round_id');
            
            // Drop old judge-based unique constraints (SQLite: drop before adding new)
            $table->dropUnique('unique_criteria_score');
            $table->dropUnique('unique_round_score');
        });
        
        // Add new unique constraint for quiz bee: unique per contestant per round per question
        Schema::table('scores', function (Blueprint $table) {
            $table->unique(['event_id', 'contestant_id', 'round_id', 'question_number'], 'unique_quizbee_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            // Drop quiz bee constraint
            $table->dropUnique('unique_quizbee_score');
            
            // Drop question_number column
            $table->dropColumn('question_number');
        });
        
        // Restore old constraints
        Schema::table('scores', function (Blueprint $table) {
            $table->unique(['event_id', 'contestant_id', 'judge_id', 'criteria_id'], 'unique_criteria_score');
            $table->unique(['event_id', 'contestant_id', 'judge_id', 'round_id'], 'unique_round_score');
        });
    }
};

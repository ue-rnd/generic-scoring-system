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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('contestant_id')->constrained()->onDelete('cascade');
            $table->foreignId('judge_id')->constrained()->onDelete('cascade');
            $table->foreignId('criteria_id')->nullable()->constrained()->onDelete('cascade'); // For criteria-based judging
            $table->foreignId('round_id')->nullable()->constrained()->onDelete('cascade'); // For rounds-based judging
            $table->decimal('score', 8, 2);
            $table->text('comments')->nullable();
            $table->timestamps();
            
            // Ensure unique scoring per judge per contestant per criteria/round
            $table->unique(['event_id', 'contestant_id', 'judge_id', 'criteria_id'], 'unique_criteria_score');
            $table->unique(['event_id', 'contestant_id', 'judge_id', 'round_id'], 'unique_round_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};

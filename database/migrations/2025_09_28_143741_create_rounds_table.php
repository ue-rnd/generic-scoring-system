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
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('total_questions')->default(0);
            $table->decimal('points_per_question', 8, 2)->default(0);
            $table->decimal('max_score', 8, 2); // Maximum possible score for this round
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0); // For ordering rounds
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rounds');
    }
};

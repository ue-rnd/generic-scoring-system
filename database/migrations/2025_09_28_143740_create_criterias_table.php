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
        Schema::create('criterias', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('weight', 5, 2)->default(1.00); // Weight for final calculation
            $table->decimal('max_score', 8, 2); // Maximum possible score
            $table->decimal('min_score', 8, 2)->default(0); // Minimum possible score
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0); // For ordering criteria
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criterias');
    }
};

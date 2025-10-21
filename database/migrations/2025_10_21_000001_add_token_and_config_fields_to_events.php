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
        Schema::table('events', function (Blueprint $table) {
            // Public viewing token and configuration
            $table->string('public_viewing_token', 64)->unique()->nullable()->after('is_active');
            $table->json('public_viewing_config')->nullable()->after('public_viewing_token');
            
            // Quiz bee scoring mode: 'boolean' (correct/incorrect) or 'manual' (enter score)
            $table->enum('scoring_mode', ['boolean', 'manual'])->default('manual')->after('judging_type');
            
            // Admin access token for managing event
            $table->string('admin_token', 64)->unique()->nullable()->after('public_viewing_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Drop unique indexes first (SQLite requirement)
            $table->dropUnique(['public_viewing_token']);
            $table->dropUnique(['admin_token']);
            
            $table->dropColumn([
                'public_viewing_token',
                'public_viewing_config',
                'scoring_mode',
                'admin_token'
            ]);
        });
    }
};

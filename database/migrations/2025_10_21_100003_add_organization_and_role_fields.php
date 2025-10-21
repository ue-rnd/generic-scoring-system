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
        // Add super_admin flag to users
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('email');
        });

        // Add organization_id to events (replace organizer_id)
        Schema::table('events', function (Blueprint $table) {
            // Drop old organizer_id
            $table->dropForeign(['organizer_id']);
            $table->dropColumn('organizer_id');
            
            // Add new organization fields
            $table->foreignId('organization_id')->nullable()->after('judging_type')->constrained()->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->after('organization_id')->constrained('users')->nullOnDelete();
        });

        // Add organization_id to contestants
        Schema::table('contestants', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('event_id')->constrained()->nullOnDelete();
        });

        // Add organization_id to judges
        Schema::table('judges', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('specialization')->constrained()->nullOnDelete();
        });

        // Add organization_id to criterias
        Schema::table('criterias', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('event_id')->constrained()->nullOnDelete();
        });

        // Add organization_id to rounds
        Schema::table('rounds', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('event_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['created_by_user_id']);
            $table->dropColumn(['organization_id', 'created_by_user_id']);
            
            // Restore organizer_id
            $table->foreignId('organizer_id')->after('judging_type')->constrained('users')->onDelete('cascade');
        });

        Schema::table('contestants', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });

        Schema::table('judges', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });

        Schema::table('criterias', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });

        Schema::table('rounds', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });
    }
};

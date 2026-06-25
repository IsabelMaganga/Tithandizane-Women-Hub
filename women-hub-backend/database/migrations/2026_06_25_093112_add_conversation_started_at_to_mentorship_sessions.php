<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mentorship_sessions', function (Blueprint $table) {
            // Only add columns that don't exist yet.
            // missed_at already exists so it is excluded.

            if (!Schema::hasColumn('mentorship_sessions', 'conversation_started_at')) {
                $table->timestamp('conversation_started_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('mentorship_sessions', 'ended_at')) {
                $table->timestamp('ended_at')->nullable()->after('conversation_started_at');
            }

            if (!Schema::hasColumn('mentorship_sessions', 'mentor_notes')) {
                $table->text('mentor_notes')->nullable()->after('ended_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mentorship_sessions', function (Blueprint $table) {
            $table->dropColumnIfExists('conversation_started_at');
            $table->dropColumnIfExists('ended_at');
            $table->dropColumnIfExists('mentor_notes');
        });
    }
};
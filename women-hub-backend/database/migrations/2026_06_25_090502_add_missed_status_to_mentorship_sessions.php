<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Run:  php artisan migrate
 *
 * What this does:
 *  1. Adds 'missed' to the status enum on mentorship_sessions
 *  2. Creates the notifications table if it doesn't exist yet
 *     (Laravel's built-in migration usually handles this, but included here
 *      in case it was skipped)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Add 'missed' to the status column ─────────────────────────────
        // MySQL ENUM: we have to redefine the whole column to add a value.
        Schema::table('mentorship_sessions', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'accepted',
                'declined',
                'completed',
                'missed',       // ← new value
            ])->default('pending')->change();

            // Add missed_at timestamp so we know exactly when it was flagged
            $table->timestamp('missed_at')->nullable()->after('ended_at');
        });

        // ── 2. Notifications table (skip if it already exists) ────────────────
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('mentorship_sessions', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'accepted',
                'declined',
                'completed',
            ])->default('pending')->change();

            $table->dropColumn('missed_at');
        });
    }
};
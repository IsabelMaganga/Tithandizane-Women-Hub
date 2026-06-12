<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignedMentorIdToHarassmentReportsTable extends Migration
{
    public function up()
    {
        Schema::table('harassment_reports', function (Blueprint $table) {
            // Add assigned_mentor_id column if it doesn't exist
            if (!Schema::hasColumn('harassment_reports', 'assigned_mentor_id')) {
                $table->foreignId('assigned_mentor_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            }
            
            // Add other missing columns if they don't exist
            if (!Schema::hasColumn('harassment_reports', 'admin_response')) {
                $table->text('admin_response')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('harassment_reports', 'responded_at')) {
                $table->timestamp('responded_at')->nullable()->after('admin_response');
            }
        });
    }

    public function down()
    {
        Schema::table('harassment_reports', function (Blueprint $table) {
            $table->dropForeign(['assigned_mentor_id']);
            $table->dropColumn(['assigned_mentor_id', 'admin_response', 'responded_at']);
        });
    }
}
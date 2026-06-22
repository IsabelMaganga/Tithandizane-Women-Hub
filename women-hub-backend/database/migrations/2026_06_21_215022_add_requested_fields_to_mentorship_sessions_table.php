<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestedFieldsToMentorshipSessionsTable extends Migration
{
    public function up(): void
    {
        Schema::table('mentorship_sessions', function (Blueprint $table) {
            $table->date('requested_date')->nullable()->after('status');
            $table->time('requested_time_from')->nullable()->after('requested_date');
            $table->time('requested_time_to')->nullable()->after('requested_time_from');
        });
    }

    public function down(): void
    {
        Schema::table('mentorship_sessions', function (Blueprint $table) {
            $table->dropColumn(['requested_date', 'requested_time_from', 'requested_time_to']);
        });
    }
}
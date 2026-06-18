<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToHarassmentReportsTable extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('harassment_reports', 'user_id')) {
            Schema::table('harassment_reports', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('harassment_reports', 'user_id')) {
            Schema::table('harassment_reports', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
}

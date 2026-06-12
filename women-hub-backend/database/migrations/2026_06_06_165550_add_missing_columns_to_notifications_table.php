<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToNotificationsTable extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Check and add user_id column
            if (!Schema::hasColumn('notifications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            
            // Check and add report_id column
            if (!Schema::hasColumn('notifications', 'report_id')) {
                $table->unsignedBigInteger('report_id')->nullable()->after('user_id');
            }
            
            // Check and add type column
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->nullable()->after('id');
            }
            
            // Check and add title column
            if (!Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable()->after('message');
            }
            
            // Check and add message column
            if (!Schema::hasColumn('notifications', 'message')) {
                $table->text('message')->nullable()->after('title');
            }
            
            // Check and add data column
            if (!Schema::hasColumn('notifications', 'data')) {
                $table->text('data')->nullable()->after('message');
            }
            
            // Check and add is_read column
            if (!Schema::hasColumn('notifications', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('data');
            }
            
            // Check and add read_at column
            if (!Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            }
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $columns = ['user_id', 'report_id', 'type', 'title', 'message', 'data', 'is_read', 'read_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('notifications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
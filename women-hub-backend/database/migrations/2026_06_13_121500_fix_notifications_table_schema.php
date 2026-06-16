<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixNotificationsTableSchema extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('notifications')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        Schema::create('notifications_temp', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('report_id')->nullable();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->string('notifiable_type')->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();
        });

        $rows = DB::table('notifications')->get();

        foreach ($rows as $row) {
            DB::table('notifications_temp')->insert([
                'type' => $row->type,
                'user_id' => $row->user_id,
                'report_id' => $row->report_id,
                'title' => $row->title,
                'message' => $row->message,
                'data' => $row->data,
                'is_read' => $row->is_read,
                'read_at' => $row->read_at,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
                'notifiable_type' => $row->notifiable_type ?? null,
                'notifiable_id' => $row->notifiable_id ?? null,
            ]);
        }

        Schema::drop('notifications');
        Schema::rename('notifications_temp', 'notifications');

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite can't modify columns so we recreate
        Schema::dropIfExists('app_notifications');

        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id(); // ← auto-increment integer, replaces uuid
            $table->string('type');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('report_id')->nullable();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('report_id')
                  ->references('id')->on('harassment_reports')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
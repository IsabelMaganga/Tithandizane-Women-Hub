<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('notifications');

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();        // Laravel needs uuid, NOT auto-increment
            $table->string('type');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('report_id')->nullable();
            $table->string('title')->nullable();  // nullable — Laravel never writes this
            $table->text('message')->nullable();  // nullable — Laravel never writes this
            $table->json('data')->nullable();     // Laravel writes everything here
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('report_id')->references('id')->on('harassment_reports')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
}
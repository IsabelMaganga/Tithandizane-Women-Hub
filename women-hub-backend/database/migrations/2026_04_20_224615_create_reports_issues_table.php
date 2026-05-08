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
        Schema::create('reports_issues', function (Blueprint $table) {
            $table->id();
            $table->string('username')->default('mentor');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['bug','feedback','request','other']);
            $table->dateTime('issue_date');
            $table->enum('status', ['open','pending','resolved'])->default('pending');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports_issues');
    }
};

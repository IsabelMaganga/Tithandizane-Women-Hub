<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->string('role')->default('user'); // admin, mentor, user
    
    // Mentor specific fields (make them nullable)
    $table->string('phone')->nullable();
    $table->string('location')->nullable();
    $table->string('photo')->nullable();
    $table->json('expertise_area')->nullable(); 
    $table->text('bio')->nullable();
    $table->string('status')->default('active');
    $table->boolean('is_available')->default(true);
    $table->json('available_days')->nullable();
    $table->time('available_time_start')->nullable();
    $table->time('available_time_end')->nullable();
    $table->string('linkedin_url')->nullable();
    $table->string('twitter_url')->nullable();
    $table->string('website_url')->nullable();
    $table->text('notes')->nullable();
    $table->boolean('notify_welcome')->default(false);
    $table->boolean('notify_training')->default(false);

    $table->rememberToken();
    $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
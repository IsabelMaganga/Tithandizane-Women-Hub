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
            $table->enum('role', ['user', 'mentor', 'admin'])->default('user');
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('photo')->nullable();
            $table->json('expertise')->nullable();
            $table->text('bio')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', [ 'active', 'inactive'])->default('active');
            $table->string('availability')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('expertise_area')->nullable(); // for mentors
            $table->boolean('is_available')->default(true); // for mentors
            $table->json('available_days')->nullable(); // e.g. ["Monday","Wednesday","Friday"]
            $table->string('available_time_start')->nullable(); // e.g. "09:00"
            $table->string('available_time_end')->nullable();   // e.g. "17:00"
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_resets');
    }
};

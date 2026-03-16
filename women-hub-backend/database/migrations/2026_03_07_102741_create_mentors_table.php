<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->text('bio');
            $table->string('area_of_support'); // menstrual_hygiene, general_issues, both
            $table->json('available_days'); // ['Monday','Wednesday','Friday']
            $table->string('available_time_from'); // e.g. 09:00
            $table->string('available_time_to');   // e.g. 17:00
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentors');
    }
};
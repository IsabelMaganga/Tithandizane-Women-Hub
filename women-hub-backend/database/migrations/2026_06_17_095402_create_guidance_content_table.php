<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guidance_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->longText('body');
            $table->enum('category', ['menstrual_hygiene', 'general']);
            $table->enum('status', ['published', 'unpublished'])->default('unpublished');
            $table->enum('language', ['english'])->default('english');
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index('mentor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guidance_contents');
    }
};

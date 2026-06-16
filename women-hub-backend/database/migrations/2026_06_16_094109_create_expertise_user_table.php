<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('expertise_user', function (Blueprint $table) {
        $table->id();
        // Links to the users table
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // Links to the expertises table
        $table->foreignId('expertise_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expertise_user');
    }
};

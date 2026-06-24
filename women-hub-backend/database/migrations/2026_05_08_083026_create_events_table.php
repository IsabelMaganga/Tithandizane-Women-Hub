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
       Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->string('type')->default('general'); // training, workshop, meeting, general
            $table->string('status')->default('upcoming'); // upcoming, ongoing, completed, cancelled
            $table->string('color')->default('#7c3aed'); // purple by default
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);
            $table->timestamps();
        });

        // Create event_participants pivot table
        Schema::create('event_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_participants');
        Schema::dropIfExists('events');
    }
};

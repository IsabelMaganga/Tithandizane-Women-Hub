<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harassment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('incident_type'); // verbal, physical, online, workplace, etc.
            $table->text('description');
            $table->string('location')->nullable();
            $table->date('incident_date')->nullable();
            $table->string('perpetrator_info')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->enum('status', ['submitted', 'under_review', 'resolved', 'closed'])->default('submitted');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harassment_reports');
    }
};
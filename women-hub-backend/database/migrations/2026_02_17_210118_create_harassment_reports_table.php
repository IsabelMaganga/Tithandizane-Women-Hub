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
            $table->string('reference_number')->unique(); // auto-generated e.g. RPT-2026-0001
            $table->string('reporter_name')->nullable();  // nullable for anonymous
            $table->string('reporter_contact')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('incident_type'); // verbal, physical, online, other
            $table->date('incident_date')->nullable();
            $table->string('incident_location')->nullable();
            $table->text('description');
            $table->string('perpetrator_info')->nullable();
            $table->enum('status', ['new', 'under_review', 'resolved', 'closed'])->default('new');
            $table->text('admin_notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harassment_reports');
    }
};
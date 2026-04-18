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
    // Add this line to link the report to a user
    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
    
    $table->string('incident_type');
    $table->text('description');
    $table->string('incident_location')->nullable();
    $table->date('incident_date')->nullable();
    $table->string('perpetrator_info')->nullable();
    $table->boolean('is_anonymous')->default(false);
    // Note: Change 'submitted' to 'new' or update your Controller to match these enums
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
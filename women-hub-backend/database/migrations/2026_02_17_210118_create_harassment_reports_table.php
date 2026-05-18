<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('harassment_reports', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->enum('incident_type', ['physical', 'verbal', 'sexual', 'cyber', 'other'])->default('other');
            $table->string('incident_title');
            $table->text('incident_description');
            $table->date('incident_date');
            $table->string('incident_location');
            $table->text('perpetrator_info')->nullable();
            
            // Anonymous  victim info
            $table->boolean('is_anonymous')->default(true);
            $table->string('victim_name')->nullable();
            $table->string('victim_email')->nullable();
            $table->string('victim_phone')->nullable();
            
            // Admin fields
            $table->enum('status', ['pending', 'reviewing', 'resolved', 'dismissed'])->default('pending');
            $table->text('admin_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users');
            
            // User association if logged in
            $table->foreignId('user_id')->nullable()->constrained();
            
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('status');
            $table->index('incident_type');
            $table->index('created_at');
            $table->index('reference_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('harassment_reports');
    }
};
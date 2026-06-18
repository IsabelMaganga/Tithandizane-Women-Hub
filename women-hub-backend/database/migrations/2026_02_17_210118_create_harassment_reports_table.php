<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHarassmentReportsTable extends Migration
{
    public function up()
    {
        Schema::create('harassment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->string('reference_number')->unique();
            $table->string('incident_type');
            $table->string('incident_title');
            $table->text('incident_description');
            $table->date('incident_date');
            $table->string('incident_location');
            $table->text('perpetrator_info')->nullable();
            $table->boolean('is_anonymous')->default(true);
            $table->string('victim_name')->nullable();
            $table->string('victim_email')->nullable();
            $table->string('victim_phone')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'assigned', 'resolved', 'dismissed'])->default('pending');
            $table->text('admin_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->unsignedBigInteger('assigned_mentor_id')->nullable();
            $table->timestamps();
            
            $table->foreign('assigned_mentor_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['status', 'assigned_mentor_id']);
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('harassment_reports');
    }
}
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
        // Create events table if it doesn't exist
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->datetime('start')->nullable();
                $table->datetime('end')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->string('location')->nullable();
                $table->string('type')->default('general');
                $table->string('status')->default('upcoming');
                $table->string('color')->default('#7c3aed');
                $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
                $table->integer('max_participants')->nullable();
                $table->integer('current_participants')->default(0);
                $table->timestamps();
            });
        } else {
            // Add new fields if table exists
            Schema::table('events', function (Blueprint $table) {
                if (!Schema::hasColumn('events', 'start_date')) {
                    $table->date('start_date')->after('description')->nullable();
                    $table->date('end_date')->after('start_date')->nullable();
                    $table->time('start_time')->after('end_date')->nullable();
                    $table->time('end_time')->after('start_time')->nullable();
                    $table->string('location')->nullable()->after('end_time');
                    $table->string('type')->default('general')->after('location');
                    $table->string('status')->default('upcoming')->after('type');
                    $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete()->after('status');
                    $table->integer('max_participants')->nullable()->after('created_by');
                    $table->integer('current_participants')->default(0)->after('max_participants');
                }
            });
        }

        // Create event_participants pivot table
        if (!Schema::hasTable('event_participants')) {
            Schema::create('event_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamp('registered_at')->useCurrent();
                $table->timestamps();

                $table->unique(['event_id', 'user_id']);
            });
        }
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

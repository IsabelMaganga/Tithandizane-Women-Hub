<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mentors', function (Blueprint $table) {
            // Add missing columns ONLY if they don't exist
            
            // Basic fields
            if (!Schema::hasColumn('mentors', 'location')) {
                $table->string('location')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('mentors', 'phone')) {
                $table->string('phone')->nullable()->after('location');
            }
            
            // Password is critical - check if it exists
            if (!Schema::hasColumn('mentors', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            
            // Remember token for authentication
            if (!Schema::hasColumn('mentors', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
            
            // Professional fields
            if (!Schema::hasColumn('mentors', 'availability')) {
                $table->string('availability')->nullable()->after('bio');
            }
            
            // Schedule fields - check if they exist, if not add them
            if (!Schema::hasColumn('mentors', 'available_days')) {
                $table->json('available_days')->nullable()->after('availability');
            }
            
            // Handle the time field naming - your table has available_time_from/available_time_to
            if (!Schema::hasColumn('mentors', 'available_time_start') && !Schema::hasColumn('mentors', 'available_time_from')) {
                $table->string('available_time_start')->nullable()->after('available_days');
            } elseif (!Schema::hasColumn('mentors', 'available_time_start') && Schema::hasColumn('mentors', 'available_time_from')) {
                // Rename existing column to match expected name
                $table->renameColumn('available_time_from', 'available_time_start');
            }
            
            if (!Schema::hasColumn('mentors', 'available_time_end') && !Schema::hasColumn('mentors', 'available_time_to')) {
                $table->string('available_time_end')->nullable()->after('available_time_start');
            } elseif (!Schema::hasColumn('mentors', 'available_time_end') && Schema::hasColumn('mentors', 'available_time_to')) {
                // Rename existing column to match expected name
                $table->renameColumn('available_time_to', 'available_time_end');
            }
            
            // Handle expertise field (your table has area_of_support)
            if (!Schema::hasColumn('mentors', 'expertise') && !Schema::hasColumn('mentors', 'area_of_support')) {
                $table->text('expertise')->nullable()->after('bio');
            } elseif (!Schema::hasColumn('mentors', 'expertise') && Schema::hasColumn('mentors', 'area_of_support')) {
                // Rename existing column to match expected name
                $table->renameColumn('area_of_support', 'expertise');
            }
            
            // Social links
            if (!Schema::hasColumn('mentors', 'linkedin_url')) {
                $table->string('linkedin_url')->nullable()->after('availability');
            }
            
            if (!Schema::hasColumn('mentors', 'twitter_url')) {
                $table->string('twitter_url')->nullable()->after('linkedin_url');
            }
            
            if (!Schema::hasColumn('mentors', 'website_url')) {
                $table->string('website_url')->nullable()->after('twitter_url');
            }
            
            // Additional fields
            if (!Schema::hasColumn('mentors', 'notes')) {
                $table->text('notes')->nullable()->after('website_url');
            }
            
            if (!Schema::hasColumn('mentors', 'notify_welcome')) {
                $table->boolean('notify_welcome')->default(false)->after('notes');
            }
            
            if (!Schema::hasColumn('mentors', 'notify_training')) {
                $table->boolean('notify_training')->default(false)->after('notify_welcome');
            }
        });
    }

    public function down()
    {
        Schema::table('mentors', function (Blueprint $table) {
            // Only drop columns that exist
            $columns = [
                'location', 
                'phone', 
                'availability',
                'available_days',
                'available_time_start',
                'available_time_end',
                'linkedin_url', 
                'twitter_url', 
                'website_url', 
                'notes', 
                'notify_welcome', 
                'notify_training'
            ];
            
            // Only drop columns that actually exist
            foreach ($columns as $column) {
                if (Schema::hasColumn('mentors', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Don't drop password or remember_token as they're critical
        });
    }
};
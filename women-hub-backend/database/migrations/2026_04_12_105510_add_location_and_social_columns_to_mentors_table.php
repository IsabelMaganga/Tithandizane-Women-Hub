<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mentors', function (Blueprint $table) {
            // Add missing columns
            $table->string('location')->nullable()->after('email');
            $table->string('phone')->nullable()->after('location');
            $table->string('availability')->nullable()->after('bio');
            $table->string('linkedin_url')->nullable()->after('availability');
            $table->string('twitter_url')->nullable()->after('linkedin_url');
            $table->string('website_url')->nullable()->after('twitter_url');
            $table->text('notes')->nullable()->after('website_url');
            $table->boolean('notify_welcome')->default(false)->after('notes');
            $table->boolean('notify_training')->default(false)->after('notify_welcome');
        });
    }

    public function down()
    {
        Schema::table('mentors', function (Blueprint $table) {
            $table->dropColumn([
                'location', 
                'phone', 
                'availability', 
                'linkedin_url', 
                'twitter_url', 
                'website_url', 
                'notes', 
                'notify_welcome', 
                'notify_training'
            ]);
        });
    }
};
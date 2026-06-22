<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mentorship_sessions', function (Blueprint $table) {
            $table->foreignId('conversation_id')
                  ->nullable()
                  ->constrained('conversations')
                  ->nullOnDelete()
                  ->after('conversation_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('mentorship_sessions', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn('conversation_id');
        });
    }
};
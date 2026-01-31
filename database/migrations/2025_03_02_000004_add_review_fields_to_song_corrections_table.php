<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('song_corrections', function (Blueprint $table) {
            $table->foreignId('reviewed_by')->nullable()->after('user_id')->constrained('users');
            $table->timestamp('reviewed_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('song_corrections', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['reviewed_by', 'reviewed_at']);
        });
    }
};

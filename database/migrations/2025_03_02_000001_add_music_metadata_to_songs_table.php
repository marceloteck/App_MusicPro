<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->string('singer')->nullable()->after('artist');
            $table->string('composer')->nullable()->after('singer');
            $table->text('description')->nullable()->after('genre');
            $table->string('video_url')->nullable()->after('description');
            $table->longText('chords')->nullable()->after('lyrics');
        });
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['singer', 'composer', 'description', 'video_url', 'chords']);
        });
    }
};

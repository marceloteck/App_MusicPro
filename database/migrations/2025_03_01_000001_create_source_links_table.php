<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('source_links', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique();
            $table->string('status')->default('pending');
            $table->text('message')->nullable();
            $table->foreignId('song_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_links');
    }
};

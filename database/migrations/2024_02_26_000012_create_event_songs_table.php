<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('song_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Garante que uma música não pode ser adicionada duas vezes no mesmo evento
            $table->unique(['event_id', 'song_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_songs');
    }
}; 
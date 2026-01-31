<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('song_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Garante que um usuário não pode favoritar a mesma música duas vezes
            $table->unique(['user_id', 'song_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorites');
    }
}; 
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('band_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('band_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role'); // 'admin', 'editor', 'viewer'
            $table->string('instrument')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Garante que um usuário não pode ter múltiplos papéis na mesma banda
            $table->unique(['band_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('band_members');
    }
}; 
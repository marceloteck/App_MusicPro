<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Inserir roles padrão
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'description' => 'Administrador do sistema',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'editor',
                'description' => 'Editor de conteúdo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'viewer',
                'description' => 'Visualizador',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}; 
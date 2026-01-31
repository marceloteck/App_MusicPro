<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Inserir permissões padrão
        DB::table('permissions')->insert([
            [
                'name' => 'create_band',
                'description' => 'Criar novas bandas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'edit_band',
                'description' => 'Editar bandas existentes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'delete_band',
                'description' => 'Excluir bandas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'view_band',
                'description' => 'Visualizar bandas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'manage_users',
                'description' => 'Gerenciar usuários',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}; 
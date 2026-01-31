<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('user_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Associar permissões aos roles
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $editorRole = DB::table('roles')->where('name', 'editor')->first();
        $viewerRole = DB::table('roles')->where('name', 'viewer')->first();

        $permissions = DB::table('permissions')->get();

        foreach ($permissions as $permission) {
            // Admin tem todas as permissões
            DB::table('role_permission')->insert([
                'role_id' => $adminRole->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Editor tem permissões limitadas
            if (in_array($permission->name, ['create_band', 'edit_band', 'view_band'])) {
                DB::table('role_permission')->insert([
                    'role_id' => $editorRole->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Viewer tem apenas permissão de visualização
            if ($permission->name === 'view_band') {
                DB::table('role_permission')->insert([
                    'role_id' => $viewerRole->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('role_permission');
    }
}; 
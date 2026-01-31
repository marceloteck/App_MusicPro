<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    // Relacionamento many-to-many com permissões
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    // Relacionamento many-to-many com usuários
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    // Método para verificar se o role tem uma permissão específica
    public function hasPermission($permission)
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    // Método para atribuir permissões ao role
    public function givePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }
        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    // Método para remover permissão do role
    public function revokePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }
        $this->permissions()->detach($permission->id);
    }
} 
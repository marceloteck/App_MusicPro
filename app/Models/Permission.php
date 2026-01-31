<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    // Relacionamento: uma permissão pertence a um integrante da banda
    public function bandMember()
    {
        return $this->belongsTo(BandMember::class);
    }

    // Relacionamento many-to-many com roles
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    // Método para verificar se a permissão está atribuída a um role
    public function isAssignedTo($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        return $this->roles()->where('role_id', $role->id)->exists();
    }
}

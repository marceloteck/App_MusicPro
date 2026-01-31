<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BandMember extends Model
{
    use HasFactory;

    protected $fillable = ['band_id', 'user_id', 'role'];

    // Relacionamento: um integrante pertence a uma banda
    public function band()
    {
        return $this->belongsTo(Band::class);
    }

    // Relacionamento: um integrante pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento: um integrante pode ter várias permissões
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
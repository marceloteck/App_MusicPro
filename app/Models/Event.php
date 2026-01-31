<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'band_id',
        'title',
        'date',
        'description'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    // Relacionamento: um evento pertence a uma banda
    public function band()
    {
        return $this->belongsTo(Band::class);
    }

    // Relacionamento: um evento pode ter várias músicas
    public function songs()
    {
        return $this->hasMany(EventSong::class);
    }
}


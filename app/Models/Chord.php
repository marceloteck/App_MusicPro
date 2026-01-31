<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chord extends Model
{
    use HasFactory;

    protected $fillable = ['song_id', 'chord_progression', 'key', 'notes'];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}

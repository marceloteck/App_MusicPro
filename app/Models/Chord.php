<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chord extends Model
{
    use HasFactory;

    protected $fillable = ['song_id', 'line_number', 'lyrics', 'chords'];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSong extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'song_id'];

    // Relacionamento: uma música pertence a um evento
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Relacionamento: uma música pertence a uma música cadastrada
    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}


<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist',
        'lyrics',
        'chords',
        'video_url',
        'file_path',
        'source_url',
        'imported_at',
    ];

    // Relacionamento: uma música pode estar em vários eventos
    public function events()
    {
        return $this->hasMany(EventSong::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class);
    }
}

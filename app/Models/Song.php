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
        'singer',
        'composer',
        'description',
        'lyrics',
        'chords',
        'video_url',
        'audio_url',
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

    public function corrections()
    {
        return $this->hasMany(SongCorrection::class);
    }

    public function versions()
    {
        return $this->hasMany(SongVersion::class);
    }
}

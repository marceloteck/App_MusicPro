<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongCorrection extends Model
{
    use HasFactory;

    protected $fillable = [
        'song_id',
        'user_id',
        'reviewed_by',
        'content',
        'status',
        'reviewed_at',
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

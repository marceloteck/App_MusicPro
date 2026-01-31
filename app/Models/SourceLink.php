<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'status',
        'message',
        'song_id',
        'created_by',
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

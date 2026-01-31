<?php

namespace App\Http\Controllers\Pages\Player;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Models\Song;
use Inertia\Inertia;

class PlayerController extends Controller
{
    public function index()
    {
        return Inertia::render('Pages/player/indexPlayer', [
            'playlists' => Playlist::with('songs')->latest()->get(),
            'songs' => Song::latest()->take(50)->get(),
        ]);
    }
    public function search()
    {
        return Inertia::render('Pages/player/searchPlayer');
    }
}

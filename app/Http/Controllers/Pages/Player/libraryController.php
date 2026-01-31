<?php

namespace App\Http\Controllers\Pages\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class libraryController extends Controller
{
    public function index()
    {
        return Inertia::render('Pages/player/library');
    }
}

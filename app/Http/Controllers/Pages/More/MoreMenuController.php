<?php

namespace App\Http\Controllers\Pages\More;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MoreMenuController extends Controller
{
    public function index()
    {
        return Inertia::render('Pages/More/indexMore');
    }
}

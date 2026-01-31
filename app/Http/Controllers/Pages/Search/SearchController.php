<?php

namespace App\Http\Controllers\Pages\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index()
    {
        return Inertia::render('Pages/Search/search');
    }
    public function externalSearch()
    {
        return Inertia::render('Pages/Search/search');
    }
}

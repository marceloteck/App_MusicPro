<?php
use App\Http\Controllers\Pages\Search\SearchController;


// Pesquisa interna (no banco de dados)
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// Pesquisa na internet (YouTube, Cifra Club, etc.)
Route::get('/search/external', [SearchController::class, 'externalSearch'])->name('search.external');

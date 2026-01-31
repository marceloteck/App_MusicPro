<?php
use App\Http\Controllers\Pages\Search\SearchController;


// Pesquisa interna (no banco de dados)
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// Pesquisa na internet (YouTube, Cifra Club, etc.)
Route::get('/search/external', [SearchController::class, 'externalSearch'])->name('search.external');
Route::get('/search/external/results', [SearchController::class, 'externalResults'])->name('search.external.results');
Route::post('/search/external/import', [SearchController::class, 'importExternal'])->name('search.external.import');

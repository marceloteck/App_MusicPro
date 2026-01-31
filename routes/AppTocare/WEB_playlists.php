<?php
use App\Http\Controllers\PlaylistController;

Route::middleware('auth')->group(function () {
    Route::post('/playlists', [PlaylistController::class, 'store'])->name('playlists.store');
    Route::put('/playlists/{playlist}', [PlaylistController::class, 'update'])->name('playlists.update');
    Route::delete('/playlists/{playlist}', [PlaylistController::class, 'destroy'])->name('playlists.destroy');
});

<?php
use App\Http\Controllers\Pages\SettingsController;

Route::middleware('auth')->group(function () {
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

Route::get('/favorites', [SettingsController::class, 'indexFavorites'])->name('settings.favorites');
});

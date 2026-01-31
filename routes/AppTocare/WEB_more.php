<?php
use App\Http\Controllers\Pages\More\MoreMenuController;

// Gerenciamento de eventos
Route::middleware('auth')->group(function () {
    Route::get('/more', [MoreMenuController::class, 'index'])->name('more.index'); // ✅ Listar eventos
});
<?php

use App\Http\Controllers\Admin\SongImportController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/imports', [SongImportController::class, 'index'])->name('admin.imports.index');
    Route::post('/imports', [SongImportController::class, 'store'])->name('admin.imports.store');
});

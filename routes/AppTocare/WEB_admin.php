<?php

use App\Http\Controllers\Admin\ExternalSourceController;
use App\Http\Controllers\Admin\SongCorrectionController;
use App\Http\Controllers\Admin\SongImportController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/imports', [SongImportController::class, 'index'])->name('admin.imports.index');
    Route::post('/imports', [SongImportController::class, 'store'])->name('admin.imports.store');
    Route::get('/sources', [ExternalSourceController::class, 'index'])->name('admin.sources.index');
    Route::post('/sources', [ExternalSourceController::class, 'store'])->name('admin.sources.store');
    Route::put('/sources/{externalSource}', [ExternalSourceController::class, 'update'])->name('admin.sources.update');
    Route::delete('/sources/{externalSource}', [ExternalSourceController::class, 'destroy'])->name('admin.sources.destroy');
    Route::get('/corrections', [SongCorrectionController::class, 'index'])->name('admin.corrections.index');
    Route::post('/corrections/{correction}/approve', [SongCorrectionController::class, 'approve'])->name('admin.corrections.approve');
    Route::post('/corrections/{correction}/reject', [SongCorrectionController::class, 'reject'])->name('admin.corrections.reject');
});

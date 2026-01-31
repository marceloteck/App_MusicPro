<?php 
use App\Http\Controllers\BandController;
use App\Http\Controllers\BandMemberController;

Route::middleware('auth')->group(function () {
// Gerenciamento de bandas
Route::get('/bands', [BandController::class, 'index'])->name('bands.index');
Route::post('/bands', [BandController::class, 'store'])->name('bands.store');
Route::put('/bands/{id}', [BandController::class, 'update'])->name('bands.update');
Route::delete('/bands/{id}', [BandController::class, 'destroy'])->name('bands.destroy');
Route::put('/bands/{id}/transfer', [BandController::class, 'transfer'])->name('bands.transfer');
Route::delete('/bands/{id}/leave', [BandController::class, 'leave'])->name('bands.leave');

// Gerenciar integrantes de uma banda
Route::post('/bands/{band}/members', [BandMemberController::class, 'store'])->name('bands.members.store');
Route::get('/bands/{id}', [BandMemberController::class, 'index'])->name('bands.members.index');
Route::put('/bands/{id}/members/{member_id}', [BandMemberController::class, 'update'])->name('bands.members.update');
Route::delete('/bands/{band}/members/{member}', [BandMemberController::class, 'destroy']) ->name('bands.members.destroy'); 
});

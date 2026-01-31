<?php
use App\Http\Controllers\Pages\Player\PlayerController;
use App\Http\Controllers\Pages\Player\libraryController;

//  Gerenciamento de eventos
Route::middleware('auth')->group(function () {
    Route::get('/player', [PlayerController::class, 'index'])->name('player.index'); // ✅ Listar eventos
    Route::post('/player', [PlayerController::class, 'store'])->name('player.store'); // ✅ Criar evento
    Route::get('/player/{id}', [PlayerController::class, 'show'])->name('player.show'); // ❗ Exibir detalhes (FALTA IMPLEMENTAR)
    Route::put('/player/{id}', [PlayerController::class, 'update'])->name('player.update'); // ❗ Atualizar evento (FALTA IMPLEMENTAR)
    Route::delete('/player/{id}', [PlayerController::class, 'destroy'])->name('player.destroy'); // ❗ Excluir evento (FALTA IMPLEMENTAR)
    Route::get('/search-player', [PlayerController::class, 'search'])->name('player.search');

    Route::get('/library', [libraryController::class, 'index'])->name('library.index');
});


//php artisan make:controller Pages/Player/libraryController
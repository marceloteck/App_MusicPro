<?php
use App\Http\Controllers\SongController;

// Criar, visualizar, atualizar e deletar músicas
Route::middleware('auth')->group(function () {
    Route::get('/songs', [SongController::class, 'index'])->name('songs.index'); // ✅ Listar todas as músicas
    Route::get('/songs/insert', [SongController::class, 'ViewInsert'])->name('songs.ViewInsert'); // ✅ Listar todas as músicas
    Route::get('/songs/create', [SongController::class, 'create'])->name('songs.create'); // ✅ Formulário de criação
    Route::post('/songs', [SongController::class, 'store'])->name('songs.store'); // ✅ Salvar no BD
    Route::get('/songs/{id}', [SongController::class, 'show'])->name('songs.show'); // ❗ Exibir detalhes (FALTA IMPLEMENTAR)
    Route::get('/songs/{id}/edit', [SongController::class, 'edit'])->name('songs.edit'); // ❗ Formulário de edição (FALTA IMPLEMENTAR)
    Route::put('/songs/{id}', [SongController::class, 'update'])->name('songs.update'); // ❗ Atualizar música (FALTA IMPLEMENTAR)
Route::delete('/songs/{id}', [SongController::class, 'destroy'])->name('songs.destroy'); // ❗ Excluir música (FALTA IMPLEMENTAR)
});

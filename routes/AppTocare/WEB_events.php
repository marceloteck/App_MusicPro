<?php 
use App\Http\Controllers\EventController;

/*Route::middleware('auth')->group(function () {
// Gerenciamento de eventos
Route::get('/events', [EventController::class, 'index'])->name('events.index'); // ✅ Listar eventos
Route::post('/events', [EventController::class, 'store'])->name('events.store'); // ✅ Criar evento
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show'); // ❗ Exibir detalhes (FALTA IMPLEMENTAR)
Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update'); // ❗ Atualizar evento (FALTA IMPLEMENTAR)
Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy'); // ❗ Excluir evento (FALTA IMPLEMENTAR)
});
*/

// Rotas para eventos da banda
Route::middleware(['auth'])->group(function () {
    Route::get('/bands/{band}/events', [EventController::class, 'index'])->name('bands.events.index');
    Route::post('/bands/{band}/events', [EventController::class, 'store'])->name('bands.events.store');
    Route::get('/bands/{band}/events/{event}', [EventController::class, 'show'])->name('bands.events.show');
    Route::put('/bands/{band}/events/{event}', [EventController::class, 'update'])->name('bands.events.update');
    Route::delete('/bands/{band}/events/{event}', [EventController::class, 'destroy'])->name('bands.events.destroy');
});
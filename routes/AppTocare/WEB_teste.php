<?php
use App\Http\Controllers\testeController;
use App\Http\Controllers\BandMemberController;
use App\Http\Controllers\Pages\index\HomeContollerRoutes;
use Inertia\Inertia;



// auth
Route::middleware(['guest'])->group(function () {
    //Route::resource('bands', BandController::class);
    Route::resource('band-members', BandMemberController::class);
    //Route::resource('events', EventController::class);
    //::resource('songs', SongController::class);
});


Route::get('/songs/teste', [testeController::class, 'processarMusica_teste'])->name('songs.teste');
Route::get('/songs/musica', [testeController::class, 'mostrarMusica'])->name('songs.mostrarMusica');

Route::get('/about', [HomeContollerRoutes::class, 'about'])->name('index.about');


use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {
    $email = 'marcellosh.12@gmail.com';
    try {
        Mail::raw('Teste de e-mail no Laravel', function ($message) use ($email) {
            $message->to($email)->subject('Teste de E-mail');
        });
        return 'E-mail enviado com sucesso!';
    } catch (\Exception $e) {
        return 'Erro ao enviar: ' . $e->getMessage();
    }
});






// TESTES
// Route::get('/testemail', function () { return Inertia::render('Pages/testes/email'); })->name('test');
Route::get('/teste', function () {
    return Inertia::render('Pages/testes/test');
})->name('test');


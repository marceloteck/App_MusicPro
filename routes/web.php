<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\sendEmailController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Pages\index\HomeContollerRoutes;
use App\Http\Controllers\EventController;


Route::middleware('auth')->group(function () {
    Route::get('/', [HomeContollerRoutes::class, 'index'])->name('index.home');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/offline/songs', [\App\Http\Controllers\SongController::class, 'apiIndex'])->name('offline.songs.index');
Route::get('/offline/songs/{id}', [\App\Http\Controllers\SongController::class, 'apiShow'])->name('offline.songs.show');

// EMAIL
route::post('/', [sendEmailController::class, 'Send'])->name('sendEmail');
route::get('/viewemail', [sendEmailController::class, 'view'])->name('viewemail');


//app
Route::get('/manifest.json', function () {
    return response()->json(json_decode(file_get_contents(public_path('manifest.json'))));
});

Route::get('/service-worker.js', function () {
    return response(file_get_contents(public_path('service-worker.js')), 200)
        ->header('Content-Type', 'application/javascript');
});



require __DIR__ . '/auth.php';
require __DIR__ . '/appTocare.php';
require __DIR__ . '/google.php';

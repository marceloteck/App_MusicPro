<?php
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Redireciona para o Google
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
});

// Callback do Google
Route::get('/auth/google/callback', function () {
    try {
        $googleUser = Socialite::driver('google')->user();

        // Verifica se o usuário já existe no banco de dados
        $user = User::updateOrCreate([
            'email' => $googleUser->getEmail(),
        ], [
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'password' => bcrypt(uniqid()), // Senha aleatória
        ]);

        Auth::login($user);

        return redirect('/'); // Redireciona após login
    } catch (Exception $e) {
        return redirect('/login')->withErrors(['error' => 'Falha ao autenticar com Google.']);
    }
});

// Logout
Route::post('/logout', function (Request $request) {
    Auth::logout();
    return redirect('/login');
});

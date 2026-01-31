<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        try {
            $remember = $request->has('remember'); // Verifica se o usuário marcou "Lembrar-me"

            if (!Auth::attempt($request->only('email', 'password'), $remember)) {
                return Inertia::render('Auth/Login', [
                    'status' => 'error',
                    'message' => 'E-mail ou senha inválidos!',
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::HOME);

        } catch (\Throwable $th) {
            return Inertia::render('Auth/Login', [
                'status' => 'error',
                'message' => 'Erro ao processar login!',
            ]);
        }
    }



    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

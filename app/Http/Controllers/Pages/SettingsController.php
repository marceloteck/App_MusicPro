<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Pages/Settings/SettingsIndex', [
            'phpVersion' => PHP_VERSION,
        ]);
    }
    public function indexFavorites()
    {
        return Inertia::render('Pages/Settings/FavoritesSettings', [
            'phpVersion' => PHP_VERSION,
        ]);
    }
    // Atualizar configurações do usuário
    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|string',
            'notifications' => 'required|boolean',
        ]);

        auth()->user()->update([
            'theme' => $request->theme,
            'notifications' => $request->notifications,
        ]);

        return redirect()->back()->with('success', 'Configurações atualizadas com sucesso!');
    }
}

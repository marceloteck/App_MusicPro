<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'band_id' => 'nullable|exists:bands,id',
            'is_public' => 'boolean',
        ]);

        $data['user_id'] = $request->user()->id;

        Playlist::create($data);

        return back()->with('flash', [
            'success' => 'Lista criada com sucesso.',
            'type' => 'success',
        ]);
    }

    public function update(Request $request, Playlist $playlist)
    {
        if ($playlist->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $playlist->update($data);

        return back()->with('flash', [
            'success' => 'Lista atualizada com sucesso.',
            'type' => 'success',
        ]);
    }

    public function destroy(Playlist $playlist)
    {
        if ($playlist->user_id !== $request->user()->id) {
            abort(403);
        }

        $playlist->delete();

        return back()->with('flash', [
            'success' => 'Lista removida com sucesso.',
            'type' => 'success',
        ]);
    }
}

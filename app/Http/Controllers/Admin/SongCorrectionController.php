<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SongCorrection;
use App\Models\SongVersion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SongCorrectionController extends Controller
{
    public function index()
    {
        return Inertia::render('Pages/Admin/Corrections/Index', [
            'corrections' => SongCorrection::with(['song', 'user'])
                ->latest()
                ->take(100)
                ->get(),
        ]);
    }

    public function approve(Request $request, SongCorrection $correction)
    {
        if ($correction->status !== 'pending') {
            return back()->with('flash', [
                'success' => 'Correção já revisada.',
                'type' => 'warning',
            ]);
        }

        $song = $correction->song;
        if ($song) {
            SongVersion::create([
                'song_id' => $song->id,
                'user_id' => $request->user()?->id,
                'content' => $correction->content,
                'source' => 'correction',
            ]);

            $song->update([
                'chords' => $correction->content,
            ]);
        }

        $correction->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()?->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('flash', [
            'success' => 'Correção aprovada com sucesso.',
            'type' => 'success',
        ]);
    }

    public function reject(Request $request, SongCorrection $correction)
    {
        $correction->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()?->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('flash', [
            'success' => 'Correção rejeitada.',
            'type' => 'success',
        ]);
    }
}

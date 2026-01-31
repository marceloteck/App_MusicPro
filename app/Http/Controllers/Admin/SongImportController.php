<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SourceLink;
use App\Services\SongImportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SongImportController extends Controller
{
    public function index()
    {
        return Inertia::render('Pages/Admin/Imports/Index', [
            'imports' => SourceLink::with('song')
                ->latest()
                ->take(50)
                ->get(),
        ]);
    }

    public function store(Request $request, SongImportService $importService)
    {
        $data = $request->validate([
            'urls' => 'required|string',
        ]);

        $urls = collect(preg_split('/\r\n|\r|\n/', $data['urls']))
            ->map(fn ($url) => trim($url))
            ->filter()
            ->unique();

        $results = [];

        foreach ($urls as $url) {
            $source = SourceLink::firstOrCreate(
                ['url' => $url],
                [
                    'status' => 'pending',
                    'created_by' => $request->user()?->id,
                ]
            );

            $result = $importService->importFromUrl($url);
            $source->update([
                'status' => $result['status'],
                'message' => $result['message'],
                'song_id' => $result['song']?->id,
            ]);

            $results[] = $source;
        }

        return back()->with('flash', [
            'success' => 'Importação concluída.',
            'type' => 'success',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Pages\Search;

use App\Http\Controllers\Controller;
use App\Services\ExternalSearchService;
use App\Services\SongImportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index()
    {
        return Inertia::render('Pages/Search/search');
    }
    public function externalSearch()
    {
        return Inertia::render('Pages/Search/search');
    }

    public function externalResults(Request $request, ExternalSearchService $externalSearchService)
    {
        $data = $request->validate([
            'q' => 'required|string|max:255',
        ]);

        return response()->json([
            'results' => $externalSearchService->search($data['q']),
        ]);
    }

    public function importExternal(Request $request, SongImportService $importService)
    {
        $data = $request->validate([
            'url' => 'required|url',
        ]);

        $result = $importService->importFromUrl($data['url']);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'song' => $result['song'],
        ]);
    }

    public function previewExternal(Request $request, SongImportService $importService)
    {
        $data = $request->validate([
            'url' => 'required|url',
        ]);

        $result = $importService->previewFromUrl($data['url']);

        return response()->json($result);
    }
}

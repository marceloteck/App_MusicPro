<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExternalSource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExternalSourceController extends Controller
{
    public function index()
    {
        return Inertia::render('Pages/Admin/Sources/Index', [
            'sources' => ExternalSource::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'required|url',
            'search_url' => 'nullable|string|max:255',
            'selectors' => 'nullable|array',
            'enabled' => 'boolean',
        ]);

        ExternalSource::create($data);

        return back()->with('flash', [
            'success' => 'Fonte adicionada com sucesso.',
            'type' => 'success',
        ]);
    }

    public function update(Request $request, ExternalSource $externalSource)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'required|url',
            'search_url' => 'nullable|string|max:255',
            'selectors' => 'nullable|array',
            'enabled' => 'boolean',
        ]);

        $externalSource->update($data);

        return back()->with('flash', [
            'success' => 'Fonte atualizada com sucesso.',
            'type' => 'success',
        ]);
    }

    public function destroy(ExternalSource $externalSource)
    {
        $externalSource->delete();

        return back()->with('flash', [
            'success' => 'Fonte removida com sucesso.',
            'type' => 'success',
        ]);
    }
}

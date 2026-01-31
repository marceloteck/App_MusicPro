<?php

namespace App\Http\Controllers;

use App\Models\Band;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index(Band $band)
    {
        $this->authorize('view', $band);

        $events = $band->events()->orderBy('date')->get();

        return response()->json([
            'events' => $events
        ]);
    }

    public function store(Request $request, Band $band)
    {
        try {
             $request->validate([
                'title' => 'required|string|max:255',
                'date' => 'required|date',
                'description' => 'nullable|string',
            ]);

            $event = $band->events()->create([
                'title' => $request->title,
                'date' => $request->date,
                'description' => $request->description,
            ]);


            return back()->with([
                'flash' => [
                    'success' => 'Evento criado com sucesso!',
                    'type' => 'success'
                ]
            ]);
            
        } catch (\Exception $e) {           
            return back()->with([
                'flash' => [
                    'error' => 'Erro ao criar evento: ' . $e->getMessage(),
                    'type' => 'error'
                ]
            ]);
        }
    }

    public function show(Band $band, Event $event)
    {
        $this->authorize('view', $band);

        if ($event->band_id !== $band->id) {
            abort(404);
        }

        return Inertia::render('Events/Show', [
            'event' => $event->load('songs')
        ]);
    }

    public function update(Request $request, Band $band, Event $event)
    {
        $this->authorize('createEvent', $band);

        if ($event->band_id !== $band->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        $event->update($validated);

        return response()->json([
            'message' => 'Evento atualizado com sucesso!',
            'event' => $event
        ]);
    }

    public function destroy(Band $band, Event $event)
    {
        $this->authorize('createEvent', $band);

        if ($event->band_id !== $band->id) {
            abort(404);
        }

        $event->delete();

        return response()->json([
            'message' => 'Evento excluído com sucesso!'
        ]);
    }
}

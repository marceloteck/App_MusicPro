<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Chord;
use Illuminate\Support\Facades\Storage;


class SongController_copy extends Controller
{
    public function index()
    {
        return Inertia::render('Songs/Index', [
            'songs' => Song::all()
        ]);
    }

    public function ViewInsert()
    {
        return Inertia::render('Songs/Create', [
            'songs' => Song::all()
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'video_url' => 'nullable|url',
            'chords_file' => 'nullable|file|mimes:txt',
            'chords_text' => 'nullable|string',
            'lines' => 'nullable|array',
        ]);

        try {

            // Criando a música no banco
            $song = Song::create([
                'user_id' => auth()->id() ?? 1, // Define o usuário autenticado ou usa 1 como padrão
                'title' => $request->title,
                'artist' => $request->artist,
                'video_url' => $request->video_url
            ]);

            // 1️⃣ Se o usuário enviou um arquivo TXT com as cifras e letras
            if ($request->hasFile('chords_file')) {
                $content = Storage::get($request->file('chords_file')->store('temp'));
                $this->processarCifras($song->id, $content);
            }

            // 2️⃣ Se enviou as cifras num textarea
            elseif ($request->filled('chords_text')) {
                $this->processarCifras($song->id, $request->input('chords_text'));
            }

            // 3️⃣ Se enviou um array estruturado do frontenda
            elseif ($request->has('lines')) {
                foreach ($request->input('lines') as $line) {
                    Chord::create([
                        'song_id' => $song->id,
                        'line_number' => $line['line_number'],
                        'lyrics' => $line['lyrics'],
                        'chords' => $line['chords'],
                    ]);
                }
            }

            //return redirect()->route('songs.index')->with('success', 'Música cadastrada com sucesso!');
            return response()->json(['success' => true, 'Música cadastrada com sucesso!']);
            //code...
        } catch (\Throwable $th) {
            return response()->json(['erro' => false, 'Erro ao enviar: ' . $th]);
        }
    }

    // Função para processar um texto e dividir entre cifras e letras
    private function processarCifras($song_id, $content)
    {
        $lines = explode("\n", $content);
        $lyricsArray = []; // Para armazenar a letra linha por linha
        $formattedChords = []; // Para armazenar as cifras com posição exata
        $line_number = 1;

        while (count($lines) > 1) {
            $chordLine = trim(array_shift($lines)); // Linha de cifras (se houver)
            $lyricLine = trim(array_shift($lines)); // Linha da letra

            // Processa a linha da letra normalmente
            $lyricsArray[] = $lyricLine;

            if ($chordLine) {
                $chordMap = [];
                $charIndex = 0;
                $words = explode(" ", $lyricLine); // Divide a linha da letra em palavras

                foreach ($words as $word) {
                    // Se houver um acorde na posição, armazenamos no mapa
                    if (isset($chordLine[$charIndex]) && trim($chordLine[$charIndex]) !== '') {
                        $chordMap[] = [
                            'position' => $charIndex, // Posição exata na linha
                            'chord' => trim($chordLine[$charIndex])
                        ];
                    }
                    $charIndex += strlen($word) + 1; // Avança para a próxima palavra
                }

                $formattedChords["line_{$line_number}"] = $chordMap;
            }

            $line_number++;
        }

        // Salvando no banco
        Chord::create([
            'song_id' => $song_id,
            'lyrics' => implode("\n", $lyricsArray),
            'chords' => json_encode($formattedChords, JSON_UNESCAPED_UNICODE),
        ]);
    }



    public function show(Song $song)
    {
        return Inertia::render('Songs/Show', [
            'song' => $song
        ]);
    }
}

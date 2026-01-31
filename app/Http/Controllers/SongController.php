<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\SongCorrection;
use App\Models\SongVersion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Chord;
use Illuminate\Support\Facades\Storage;


class SongController extends Controller
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

    public function show($id)
    {
        $song = Song::findOrFail($id);
        $songData = $this->carregarMusicaJson($id);

        return Inertia::render('Songs/Show', [
            'song' => $song,
            'data' => $songData,
            'versions' => $song->versions()->latest()->take(20)->get(),
        ]);
    }

    public function apiIndex()
    {
        $query = request()->query('q');
        $songsQuery = Song::query();

        if ($query) {
            $songsQuery->where(function ($builder) use ($query) {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('lyrics', 'like', "%{$query}%");
            });
        }

        return response()->json([
            'songs' => $songsQuery->select('id', 'title', 'artist', 'source_url', 'imported_at', 'lyrics', 'video_url', 'audio_url')
                ->latest()
                ->take(200)
                ->get(),
        ]);
    }

    public function apiShow($id)
    {
        $song = Song::findOrFail($id);

        return response()->json([
            'song' => $song,
            'data' => $this->carregarMusicaJson($id),
        ]);
    }

    public function storeCorrection(Request $request, Song $song)
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        SongCorrection::create([
            'song_id' => $song->id,
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'status' => 'pending',
        ]);

        return back()->with('flash', [
            'success' => 'Correção enviada para análise.',
            'type' => 'success',
        ]);
    }

    private function salvarMusicaJson($song_id, $title, $artist, $lyrics, $chords)
    {
        $filePath = "songs/{$song_id}.json";

        // Criar um array estruturado
        $data = [
            'id' => $song_id,
            'title' => $title,
            'artist' => $artist,
            'lyrics' => $lyrics, // Letras separadas por linhas
            'chords' => $chords, // Cifras formatadas e suas posições
        ];

        // Converter para JSON
        $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Salvar no diretório `storage/app/songs/`
        Storage::disk('local')->put($filePath, $jsonData);

        return $filePath; // Retorna o caminho do arquivo
    }

    private function carregarMusicaJson($song_id)
    {
        $filePath = "songs/{$song_id}.json";

        if (!Storage::disk('local')->exists($filePath)) {
            return null;
        }

        return json_decode(Storage::disk('local')->get($filePath), true);
    }

    public function CifraAt($cifras, $pattern)
    {
        $cifra = strip_tags($cifras);
        $cifra = preg_replace($pattern, "<b>$1</b>", $cifra);
        return trim($cifra);
    }

    /* ##########################################################################*/
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'singer' => 'nullable|string|max:255',
            'composer' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'audio_url' => 'nullable|url',
            'chords_text' => 'nullable|string',
            'chords_file' => 'nullable|file|mimes:txt',
        ]);

        $title = $request->input('title');
        $artist = $request->input('artist');
        $singer = $request->input('singer');
        $composer = $request->input('composer');
        $description = $request->input('description');
        $videoUrl = $request->input('video_url');
        $audioUrl = $request->input('audio_url');
        $chordsText = $request->input('chords_text');

        if (!$chordsText && $request->hasFile('chords_file')) {
            $file = $request->file('chords_file');
            $chordsText = $file ? file_get_contents($file->getPathname()) : null;
        }

        if (!$chordsText) {
            return response()->json([
                'message' => 'Envie a cifra no texto ou no arquivo.',
            ], 422);
        }

        $linesCifras = explode("\n", $chordsText);

        // Regex para reconhecer as cifras, agora apenas cifras válidas
        $regexCifra = '/((?!(?<=[ÇçáéíóúÁÉÍÓÚ]))(((\b([A-G]{1,1}((?!([\scefghiklnopqrtuvxwyz]))((add|ADD)|#m|#|º|b)?([2-9]|m(\B|$)|maj|sus|dim|\(|º)?(\/[A-G][2-9](#|º)?(b{1,1})?)?(\+)?(\/([A-G]|[2-9]))?(º|#|b|\([2-9]\))?([2-9]|m)?(\+|(?<=[2-9])M)?(11|13)?(\/)?([A-G])?)([Eb|Bb])?(\([0-9]?[0-9]?(\/)?[0-9]?[0-9]?)?(\/1[1-9])?\b(\(((2|4|5|6|7|9|11|13)|[bB][2-9])\))?(-|\/[A-G])?(\/[2-9])?([2-9]|\)|\((b13|b11|b5)\)|\([2-9]\)|-|\/)?(\+)?)(m|b|º|#|13|add[2-9]\))?)|\b(?(?<=(?<=\s)[A-G](?=\s)))[A-G](?!(\s)?[a-zH-Z])\b)(º)?)((\(([0-9]{1,2})?(\-)?(\+)?\))|(?!((?:[çáéíóúÇÁÉÍÓÚ])|((\t|)[a-zA-Z][A-Z]|[A-G][a-z]\w|\s[A-G]\w[H-Z])\w?)))|(?(?<=(?<=\s)[A-G]M|[A-G]m|[A-G]m(?=\s)))([A-G]m|[A-G]M(?!\s))(#|b)?([2-9]|maj|sus|dim|º|º)?(\/[A-G][2-9](#)?(b{1,1})?)?(\+)?(\/([A-G]|[2-9]))?(#|b|\([2-9]\))?([2-9])?(\+)?(\([A-G][2-9]\))?(\([1-9][1-9]?\))?(11|13)?(-|\/[A-G])?(\/[2-9])?([2-9]|-|\/)?(\+)?(b|#|13)?(?!(\s)?[a-zH-Z])(?!(?:[,çáéíóúÇÁÉÍÓÚ]))\W)/';

        // Regex para detectar se há palavras que não são cifras
        $regexNaoCifra = '/\b(?![A-G][#b]?(m|maj|min|dim|sus|7|9|11|13|add|M|m7|b7|aug|\+|\/?[A-G]?[#b]?)+\b)[a-zA-ZÀ-ÿ]+\b/';

        $lyricsLines = [];

        foreach ($linesCifras as $linha) {
            // Remover espaços extras
            $linha = trim($linha);

            // Ignorar linhas vazias
            if (empty($linha)) {
                continue;
            }

            $lyricsLines[] = $linha;

            // Permitir introdução com cifras
            if (stripos($linha, "INTRO:") === 0) {
                logger()->debug('Cifras encontradas na introdução: ' . $this->CifraAt($linha, $regexCifra));
                continue;
            }

            // Se contém palavras não cifras, é uma linha de letra da música
            if (preg_match($regexNaoCifra, $linha)) {
                logger()->debug("LETRA: {$linha}");
            }
            // Se contém cifras e não contém palavras não cifras, é uma linha de cifras
            elseif (preg_match($regexCifra, $linha)) {
                logger()->debug('CIFRAS: ' . $this->CifraAt($linha, $regexCifra));
            }

        }

        $song = Song::create([
            'title' => $title,
            'artist' => $artist ?: 'Desconhecido',
            'singer' => $singer,
            'composer' => $composer,
            'description' => $description,
            'file_path' => '',
            'lyrics' => implode("\n", $lyricsLines),
            'chords' => $chordsText,
            'video_url' => $videoUrl,
            'audio_url' => $audioUrl,
        ]);

        SongVersion::create([
            'song_id' => $song->id,
            'user_id' => $request->user()?->id,
            'content' => $chordsText,
            'source' => 'manual',
        ]);

        $jsonPath = $this->salvarMusicaJson(
            $song->id,
            $song->title,
            $song->artist,
            $lyricsLines,
            []
        );

        $song->update(['file_path' => $jsonPath]);

        return response()->json([
            'message' => 'Música salva com sucesso.',
            'song' => $song,
        ]);
    }
    /* ##########################################################################*/


    /* teste versao 1  PODE SER APAGADO DAQUI*/
    public function processarMusica_teste()
    {

        $linhas = [
            "INTRO:F#m7 D A9 E",
            "F#m7 D A9 E F#m7",
            "",
            "",
            "F#m7                 D7+",
            "MÃOS NA TERRA E O CORAÇÃO",
            "A9        C#7(4)",
            "ALÉM DESTE CÉU,",
            "F#m7               D7+",
            "E A SEMENTE QUE BROTA",
            "A9             E",
            "É UM GERME DE ETERNIDADE",
            "F#m7       D/F#       A9   E",
            "VAI BROTANDO, CRESCENDO, ESPERANDO",
            "F#m7     D/F#      A9  E",
            "É A VIDA QUE VEM DESPONTAR",
            "D       A/C#",
            "E ESTE TRIGO MADURO,",
            "Bm       Bm/A   E D/F# E/G#",
            "A COLHEITA O RECOLHERÁ"
        ];

        // Regex para reconhecer as cifras, agora apenas cifras válidas
        $regexCifra = '/((?!(?<=[ÇçáéíóúÁÉÍÓÚ]))(((\b([A-G]{1,1}((?!([\scefghiklnopqrtuvxwyz]))((add|ADD)|#m|#|º|b)?([2-9]|m(\B|$)|maj|sus|dim|\(|º)?(\/[A-G][2-9](#|º)?(b{1,1})?)?(\+)?(\/([A-G]|[2-9]))?(º|#|b|\([2-9]\))?([2-9]|m)?(\+|(?<=[2-9])M)?(11|13)?(\/)?([A-G])?)([Eb|Bb])?(\([0-9]?[0-9]?(\/)?[0-9]?[0-9]?)?(\/1[1-9])?\b(\(((2|4|5|6|7|9|11|13)|[bB][2-9])\))?(-|\/[A-G])?(\/[2-9])?([2-9]|\)|\((b13|b11|b5)\)|\([2-9]\)|-|\/)?(\+)?)(m|b|º|#|13|add[2-9]\))?)|\b(?(?<=(?<=\s)[A-G](?=\s)))[A-G](?!(\s)?[a-zH-Z])\b)(º)?)((\(([0-9]{1,2})?(\-)?(\+)?\))|(?!((?:[çáéíóúÇÁÉÍÓÚ])|((\t|)[a-zA-Z][A-Z]|[A-G][a-z]\w|\s[A-G]\w[H-Z])\w?)))|(?(?<=(?<=\s)[A-G]M|[A-G]m|[A-G]m(?=\s)))([A-G]m|[A-G]M(?!\s))(#|b)?([2-9]|maj|sus|dim|º|º)?(\/[A-G][2-9](#)?(b{1,1})?)?(\+)?(\/([A-G]|[2-9]))?(#|b|\([2-9]\))?([2-9])?(\+)?(\([A-G][2-9]\))?(\([1-9][1-9]?\))?(11|13)?(-|\/[A-G])?(\/[2-9])?([2-9]|-|\/)?(\+)?(b|#|13)?(?!(\s)?[a-zH-Z])(?!(?:[,çáéíóúÇÁÉÍÓÚ]))\W)/';

        // Regex para detectar se há palavras que não são cifras
        $regexNaoCifra = '/\b(?![A-G][#b]?(m|maj|min|dim|sus|7|9|11|13|add|M|m7|b7|aug|\+|\/?[A-G]?[#b]?)+\b)[a-zA-ZÀ-ÿ]+\b/';

        $contador = 0;
        echo "<pre>";
        foreach ($linhas as $linha) {
            // Remover espaços extras
            $linha = trim($linha);

            // Ignorar linhas vazias
            /*if (empty($linha)) {
                continue;
            }*/

            // Permitir introdução com cifras
            if (stripos($linha, "INTRO:") === 0) {
                echo $this->CifraAt($linha, $regexCifra) . "\n";
                continue;
            }

            // Se contém palavras não cifras, é uma linha de letra da música
            if (preg_match($regexNaoCifra, $linha)) {
                echo "$linha\n";
            }
            // Se contém cifras e não contém palavras não cifras, é uma linha de cifras
            elseif (preg_match($regexCifra, $linha)) {
                echo $this->CifraAt($linha, $regexCifra) . "\n";
            } else {
                echo "\n";
            }

            $contador++;
        }
        echo "</pre>";

    }

    public function mostrarMusica()
    {
        $musica = json_decode(Storage::get('app/music/oracao_pela_familia.json'), true);
        return Inertia::render('Musica', ['musica' => $musica]);
    }










}






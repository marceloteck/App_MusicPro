<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Chord;
use Illuminate\Support\Facades\Storage;


class SongController__copy extends Controller
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

        return response()->json([
            'song' => $song,
            'data' => $songData
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

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'chords_text' => 'required|string'
        ]);


        // Processar cifras e letras
        $content = $request->input('chords_text');
        $lines = explode("\n", $content);
        $lyricsArray = [];
        $formattedChords = [];
        $line_number = 1;

        while (count($lines) > 1) {
            $chordLine = trim(array_shift($lines)); // Linha de cifras (se houver)
            $lyricLine = trim(array_shift($lines)); // Linha da letra

            $lyricsArray[] = $lyricLine;
            $chordMap = [];
            $charIndex = 0;
            $words = explode(" ", $lyricLine);

            foreach ($words as $word) {
                if (isset($chordLine[$charIndex]) && trim($chordLine[$charIndex]) !== '') {
                    $chordMap[] = [
                        'position' => $charIndex,
                        'chord' => trim($chordLine[$charIndex])
                    ];
                }
                $charIndex += strlen($word) + 1;
            }

            if (!empty($chordMap)) {
                $formattedChords["line_{$line_number}"] = $chordMap;
            }

            $line_number++;
        }

        // Criar a música no banco (apenas título e referência ao arquivo JSON)
        $song = Song::create([
            'title' => $request->title,
            'artist' => $request->artist,
            'file_path' => "" // Será atualizado depois
        ]);


        // Salvar JSON
        $jsonPath = $this->salvarMusicaJson($song->id, $request->title, $request->artist, $lyricsArray, $formattedChords);

        // Atualizar caminho do JSON no banco
        $song->update(['file_path' => $jsonPath]);

        return response()->json(['message' => 'Música salva com sucesso! link: ' . $jsonPath, 'song' => $song]);
    }

    public function processarMusica_teste()
    {

        $linhas = [
            "INTRO:F#m7 D A9 E",
            "F#m7 D A9 E F#m7",
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

        foreach ($linhas as $linha) {
            // Remover espaços extras
            $linha = trim($linha);

            // Ignorar linhas vazias
            if (empty($linha)) {
                continue;
            }

            // Permitir introdução com cifras
            if (stripos($linha, "INTRO:") === 0) {
                echo "Cifras encontradas na introdução: $linha\n";
                continue;
            }

            // Se contém palavras não cifras, é uma linha de letra da música
            if (preg_match($regexNaoCifra, $linha)) {
                echo "LETRA: $linha\n";
            }
            // Se contém cifras e não contém palavras não cifras, é uma linha de cifras
            elseif (preg_match($regexCifra, $linha)) {
                echo "CIFRAS: $linha\n";
            }

        }




        function processarMusica($input)
        {
            $linhas = explode("\n", $input);
            $musicaProcessada = [];
            $linhaAnteriorEraCifra = false;
            $ultimaLetraIndex = null;

            // Regex melhorada para detectar cifras
            $regexCifra = '/\b([A-G])([#b]?)(m|maj7|\+|°|dim|aug|sus2|sus4|add9|m7|7|9|11|13|7\+|6|5|b5|#5|b9|#9|b13|#11|4|2)?(?:\/[A-G][#b]?)?(\(\d+(?:[#b]?\d*)?\))?\b/';




            foreach ($linhas as $linha) {
                $linha = rtrim($linha);

                // Verifica se a linha contém cifras
                preg_match_all($regexCifra, $linha, $matches, PREG_OFFSET_CAPTURE);

                if (!empty($matches[0])) { // Se encontrou cifras
                    if ($linhaAnteriorEraCifra && $ultimaLetraIndex !== null) {
                        // Associa cifras à última linha de letra encontrada
                        foreach ($matches[0] as $match) {
                            $musicaProcessada[$ultimaLetraIndex]['chords'][] = [
                                'chord' => $match[0],
                                'position' => $match[1] // Posição na linha de cifras
                            ];
                        }
                    } else {
                        // Adiciona como linha separada de cifras
                        $musicaProcessada[] = [
                            'lyrics' => '',
                            'chords' => array_map(fn($match) => [
                                'chord' => $match[0],
                                'position' => $match[1]
                            ], $matches[0])
                        ];
                    }

                    $linhaAnteriorEraCifra = true;
                } else {
                    // Linha de letra sem cifras
                    $musicaProcessada[] = [
                        'lyrics' => $linha,
                        'chords' => []
                    ];

                    $ultimaLetraIndex = count($musicaProcessada) - 1;
                    $linhaAnteriorEraCifra = false;
                }
            }

            return $musicaProcessada;
        }






    }
}

















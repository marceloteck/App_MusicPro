<?php

namespace App\Services;

use App\Models\Song;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;

class SongImportService
{
    public function importFromUrl(string $url): array
    {
        $response = Http::timeout(20)->get($url);

        if (!$response->successful()) {
            return [
                'status' => 'failed',
                'message' => "Falha ao acessar o link (HTTP {$response->status()}).",
                'song' => null,
            ];
        }

        $html = $response->body();
        $parsed = $this->parseHtml($html, $url);

        $title = $parsed['title'] ?: 'Música importada';
        $artist = $parsed['artist'] ?: 'Desconhecido';
        $lyrics = $parsed['lyrics'];
        $videoUrl = $parsed['video_url'] ?? null;

        $song = Song::create([
            'title' => Str::limit($title, 255, ''),
            'artist' => Str::limit($artist, 255, ''),
            'file_path' => '',
            'lyrics' => $lyrics,
            'source_url' => $url,
            'imported_at' => now(),
            'video_url' => $videoUrl ?: $this->youtubeSearchUrl($title, $artist),
        ]);

        $jsonPath = $this->saveSongJson(
            $song->id,
            $song->title,
            $song->artist,
            $lyrics ? preg_split('/\r\n|\r|\n/', $lyrics) : [],
            []
        );

        $song->update(['file_path' => $jsonPath]);

        return [
            'status' => 'success',
            'message' => 'Música importada com sucesso.',
            'song' => $song,
        ];
    }

    public function previewFromUrl(string $url): array
    {
        $response = Http::timeout(20)->get($url);

        if (!$response->successful()) {
            return [
                'status' => 'failed',
                'message' => "Falha ao acessar o link (HTTP {$response->status()}).",
                'data' => null,
            ];
        }

        $parsed = $this->parseHtml($response->body(), $url);

        return [
            'status' => 'success',
            'message' => 'Pré-visualização carregada.',
            'data' => $parsed,
        ];
    }

    private function parseHtml(string $html, string $url): array
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $host = parse_url($url, PHP_URL_HOST);

        $siteData = $this->parseKnownSite($xpath, $host);
        $structuredData = $this->parseStructuredData($xpath);

        $title = $this->extractMeta($xpath, 'og:title')
            ?: $this->extractNodeValue($xpath, '//h1')
            ?: $this->extractNodeValue($xpath, '//title');

        $artist = $this->extractMeta($xpath, 'music:musician')
            ?: $this->extractMeta($xpath, 'author')
            ?: $this->extractMeta($xpath, 'og:site_name');

        $lyrics = $siteData['lyrics']
            ?? $structuredData['lyrics']
            ?? $this->extractLyrics($xpath);
        $videoUrl = $siteData['video_url'] ?? $this->extractMeta($xpath, 'og:video:url')
            ?: $this->extractMeta($xpath, 'og:video');

        return [
            'title' => $this->normalizeText($siteData['title'] ?? $structuredData['title'] ?? $title),
            'artist' => $this->normalizeText($siteData['artist'] ?? $structuredData['artist'] ?? $artist),
            'lyrics' => $lyrics ? trim($lyrics) : null,
            'video_url' => $this->normalizeText($videoUrl ?? $structuredData['video_url']),
        ];
    }

    private function extractMeta(DOMXPath $xpath, string $property): ?string
    {
        $node = $xpath->query("//meta[@property='{$property}']/@content")->item(0)
            ?: $xpath->query("//meta[@name='{$property}']/@content")->item(0);

        return $node?->nodeValue;
    }

    private function extractNodeValue(DOMXPath $xpath, string $query): ?string
    {
        $node = $xpath->query($query)->item(0);

        return $node?->textContent;
    }

    private function extractLyrics(DOMXPath $xpath): ?string
    {
        $selectors = [
            "//*[contains(@class, 'lyrics')]",
            "//*[contains(@class, 'lyric')]",
            "//*[contains(@class, 'letra')]",
            "//*[contains(@class, 'cifra')]",
            "//*[contains(@id, 'letra')]",
            "//*[contains(@id, 'lyrics')]",
            '//pre',
        ];

        foreach ($selectors as $selector) {
            $node = $xpath->query($selector)->item(0);
            if ($node) {
                return $this->normalizeText($node->textContent);
            }
        }

        return null;
    }

    private function parseKnownSite(DOMXPath $xpath, ?string $host): array
    {
        if (!$host) {
            return [];
        }

        if (str_contains($host, 'cifraclub.com.br')) {
            return $this->parseCifraClub($xpath);
        }

        if (str_contains($host, 'letras.mus.br')) {
            return $this->parseLetrasMus($xpath);
        }

        return [];
    }

    private function parseStructuredData(DOMXPath $xpath): array
    {
        $nodes = $xpath->query("//script[@type='application/ld+json']");
        $data = [];

        foreach ($nodes as $node) {
            $json = json_decode($node->textContent, true);
            if (!$json) {
                continue;
            }

            $items = is_array($json) && array_keys($json) === range(0, count($json) - 1)
                ? $json
                : [$json];

            foreach ($items as $item) {
                if (!is_array($item)) {
                    continue;
                }

                if (($item['@type'] ?? null) === 'MusicComposition') {
                    $data['title'] = $item['name'] ?? null;
                    $data['lyrics'] = $item['lyrics'] ?? null;
                    $data['artist'] = $item['composer']['name'] ?? null;
                    $data['video_url'] = $item['video'] ?? null;
                }
            }
        }

        return $data;
    }

    private function parseCifraClub(DOMXPath $xpath): array
    {
        $title = $this->extractNodeValue($xpath, "//h1[contains(@class, 't1')]");
        $artist = $this->extractNodeValue($xpath, "//h2[contains(@class, 't3')]");
        $lyrics = $this->extractNodeValue($xpath, "//*[contains(@class, 'letra')]");
        $videoUrl = $this->extractMeta($xpath, 'og:video:url')
            ?: $this->extractMeta($xpath, 'og:video');

        return [
            'title' => $title,
            'artist' => $artist,
            'lyrics' => $lyrics,
            'video_url' => $videoUrl,
        ];
    }

    private function parseLetrasMus(DOMXPath $xpath): array
    {
        $title = $this->extractNodeValue($xpath, "//h1[contains(@class, 'textStyle-primary')]");
        $artist = $this->extractNodeValue($xpath, "//div[contains(@class, 'lyric-title')]/h2");
        $lyrics = $this->extractNodeValue($xpath, "//*[contains(@class, 'lyric-original')]");
        $videoUrl = $this->extractMeta($xpath, 'og:video:url')
            ?: $this->extractMeta($xpath, 'og:video');

        return [
            'title' => $title,
            'artist' => $artist,
            'lyrics' => $lyrics,
            'video_url' => $videoUrl,
        ];
    }

    private function normalizeText(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }

    private function youtubeSearchUrl(string $title, string $artist): string
    {
        $query = trim($title . ' ' . $artist);

        return 'https://www.youtube.com/results?search_query=' . urlencode($query);
    }

    private function saveSongJson(int $songId, string $title, string $artist, array $lyrics, array $chords): string
    {
        $filePath = "songs/{$songId}.json";

        $data = [
            'id' => $songId,
            'title' => $title,
            'artist' => $artist,
            'lyrics' => $lyrics,
            'chords' => $chords,
        ];

        Storage::disk('local')->put($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $filePath;
    }
}

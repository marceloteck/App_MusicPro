<?php

namespace App\Services;

use App\Models\ExternalSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;

class ExternalSearchService
{
    public function search(string $query): array
    {
        $sources = ExternalSource::query()
            ->where('enabled', true)
            ->whereNotNull('search_url')
            ->get();

        $results = [];

        foreach ($sources as $source) {
            $searchUrl = str_replace('{query}', urlencode($query), $source->search_url);

            $response = Http::timeout(12)->get($searchUrl);
            if (!$response->successful()) {
                continue;
            }

            $selectors = $source->selectors ?? [];
            $itemSelector = $selectors['result_item'] ?? null;
            $linkSelector = $selectors['result_link'] ?? null;

            if (!$itemSelector || !$linkSelector) {
                continue;
            }

            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($response->body());
            $xpath = new DOMXPath($dom);

            $items = $xpath->query($itemSelector);
            if (!$items) {
                continue;
            }

            foreach ($items as $item) {
                $linkNode = $xpath->query($linkSelector, $item)->item(0);
                if (!$linkNode) {
                    continue;
                }

                $url = $linkNode->nodeValue;
                if ($linkNode->attributes?->getNamedItem('href')) {
                    $url = $linkNode->attributes->getNamedItem('href')->nodeValue;
                }

                if ($url && !Str::startsWith($url, 'http')) {
                    $url = rtrim($source->base_url, '/') . '/' . ltrim($url, '/');
                }

                $title = $this->extractNodeValue($xpath, $selectors['result_title'] ?? null, $item);
                $artist = $this->extractNodeValue($xpath, $selectors['result_artist'] ?? null, $item);

                if (!$url) {
                    continue;
                }

                $results[] = [
                    'url' => $url,
                    'title' => $title,
                    'artist' => $artist,
                    'source' => $source->name,
                ];

                if (count($results) >= 30) {
                    break 2;
                }
            }
        }

        return $results;
    }

    private function extractNodeValue(DOMXPath $xpath, ?string $selector, \DOMNode $context): ?string
    {
        if (!$selector) {
            return null;
        }

        $node = $xpath->query($selector, $context)->item(0);
        if (!$node) {
            return null;
        }

        return trim($node->textContent);
    }
}

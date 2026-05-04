<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GiphyController extends Controller
{
    private const ENDPOINT_SEARCH   = '/search';
    private const ENDPOINT_TRENDING = '/trending';

    public function search(Request $request)
    {
        $request->validate([
            'q'      => 'required|string|min:1|max:100',
            'limit'  => 'nullable|integer|min:1|max:50',
            'offset' => 'nullable|integer|min:0|max:5000',
        ]);

        $q      = $request->query('q');
        $limit  = (int) $request->query('limit', 24);
        $offset = (int) $request->query('offset', 0);

        // Cache 10 menit per query, hemat rate limit
        $cacheKey = 'giphy:search:' . md5("{$q}:{$limit}:{$offset}");

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($q, $limit, $offset) {
            $response = Http::timeout(8)->get(
                config('services.giphy.base') . self::ENDPOINT_SEARCH,
                [
                    'api_key' => config('services.giphy.key'),
                    'q'       => $q,
                    'limit'   => $limit,
                    'offset'  => $offset,
                    'rating'  => 'pg-13', // family-friendly default
                    'lang'    => 'en',
                ]
            );

            if (!$response->successful()) {
                return ['data' => [], 'error' => 'GIPHY unavailable'];
            }

            return $this->transformGiphyResponse($response->json());
        });

        return response()->json($data);
    }

    public function trending(Request $request)
    {
        $limit  = (int) $request->query('limit', 24);
        $offset = (int) $request->query('offset', 0);
        
        $cacheKey = 'giphy:trending:' . md5("{$limit}:{$offset}");

        $data = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($limit, $offset) {
            $response = Http::timeout(8)->get(
                config('services.giphy.base') . self::ENDPOINT_TRENDING,
                [
                    'api_key' => config('services.giphy.key'),
                    'limit'   => $limit,
                    'offset'  => $offset,
                    'rating'  => 'pg-13',
                ]
            );

            if (!$response->successful()) {
                return ['data' => [], 'error' => 'GIPHY unavailable'];
            }

            return $this->transformGiphyResponse($response->json());
        });

        return response()->json($data);
    }

    /**
     * Slim down response — kita cuma butuh field yang dipakai frontend.
     * Hemat bandwidth (response GIPHY raw bisa 200KB+ per query).
     */
    private function transformGiphyResponse(array $raw): array
    {
        $items = collect($raw['data'] ?? [])->map(function ($gif) {
            $images = $gif['images'] ?? [];
            return [
                'id'      => $gif['id'] ?? null,
                'title'   => $gif['title'] ?? '',
                // URL untuk preview di picker (kecil, hemat data)
                'preview' => $images['fixed_width_small']['url'] 
                          ?? $images['preview_gif']['url'] 
                          ?? null,
                // URL untuk dikirim ke chat (medium, ~480px wide)
                'url'     => $images['fixed_width']['url'] 
                          ?? $images['original']['url'] 
                          ?? null,
                'width'   => (int) ($images['fixed_width']['width'] ?? 200),
                'height'  => (int) ($images['fixed_width']['height'] ?? 200),
            ];
        })->filter(fn($g) => !empty($g['url']))->values();

        return [
            'data'       => $items,
            'pagination' => $raw['pagination'] ?? null,
        ];
    }
}
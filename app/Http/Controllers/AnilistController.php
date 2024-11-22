<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AnilistController extends Controller
{
    public function getAnilistId($title)
    {
        // Cache key berdasarkan judul
        $cacheKey = 'anilist_id_' . md5($title);

        // Cek cache terlebih dahulu
        return Cache::remember($cacheKey, 86400, function () use ($title) {
            $query = <<<'GRAPHQL'
            query ($search: String) {
                Media (search: $search, type: ANIME) {
                    id
                    title {
                        romaji
                        english
                        native
                    }
                    seasonYear
                    format
                }
            }
            GRAPHQL;

            try {
                $response = Http::post('https://graphql.anilist.co', [
                    'query' => $query,
                    'variables' => [
                        'search' => $title
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data']['Media']['id'] ?? null;
                }
            } catch (\Exception $e) {
                \Log::error('Anilist API Error: ' . $e->getMessage());
            }

            return null;
        });
    }
}
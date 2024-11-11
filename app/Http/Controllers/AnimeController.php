<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;

class AnimeController extends Controller
{
    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function popular()
    {
        try {
            $page = request('page', 1);
            
            // Ambil data anime populer dari Jepang
            $allAnime = collect();
            $processedIds = []; // Untuk tracking ID yang sudah diproses
            
            // Ambil data (batasi sampai 5 halaman untuk performa)
            for ($i = 1; $i <= 5; $i++) {
                $animeData = $this->tmdb->getPopularAnimePagination($i); // Update method untuk ambil dari Jepang
                if (!empty($animeData['results'])) {
                    // Filter hanya anime yang belum diproses
                    $uniqueAnime = collect($animeData['results'])->filter(function($anime) use (&$processedIds) {
                        if (in_array($anime->id, $processedIds)) {
                            return false;
                        }
                        $processedIds[] = $anime->id;
                        return true;
                    });
                    
                    $allAnime = $allAnime->merge($uniqueAnime);
                }
            }

            // Urutkan berdasarkan popularity
            $allContent = $allAnime
                ->sortByDesc('popularity')
                ->values();

            // Hitung total halaman yang sebenarnya
            $totalItems = $allContent->count();
            $itemsPerPage = 18; // Set 18 items per page
            $totalPages = ceil($totalItems / $itemsPerPage);

            // Jika halaman yang diminta melebihi total, redirect ke halaman terakhir
            if ($page > $totalPages) {
                return redirect()->route('anime.popular', ['page' => $totalPages]);
            }

            // Ambil konten untuk halaman yang diminta
            $offset = ($page - 1) * $itemsPerPage;
            $pageContent = $allContent->slice($offset, $itemsPerPage)->values();

            return view('anime.popular-anime', [
                'content' => $pageContent,
                'currentPage' => (int) $page,
                'totalPages' => $totalPages,
                'studioName' => 'Popular Anime from Japan'
            ]);

        } catch (\Exception $e) {
            \Log::error('Popular anime page error: ' . $e->getMessage());
            abort(404);
        }
    }
    public function animeMovies()
    {
        try {
            $page = request('page', 1);
            
            // Ambil data anime movies
            $allAnimeMovies = collect();
            $processedIds = []; // Untuk tracking ID yang sudah diproses
            
            // Ambil data (batasi sampai 5 halaman untuk performa)
            for ($i = 1; $i <= 5; $i++) {
                $animeData = $this->tmdb->getAnimeMovies($i); // Method untuk ambil anime movies
                if (!empty($animeData['results'])) {
                    // Filter hanya anime yang belum diproses
                    $uniqueAnimeMovies = collect($animeData['results'])->filter(function($anime) use (&$processedIds) {
                        if (in_array($anime->id, $processedIds)) {
                            return false;
                        }
                        $processedIds[] = $anime->id;
                        return true;
                    });
                    
                    $allAnimeMovies = $allAnimeMovies->merge($uniqueAnimeMovies);
                }
            }

            // Urutkan berdasarkan popularity
            $allContent = $allAnimeMovies
                ->sortByDesc('popularity')
                ->values();

            // Hitung total halaman yang sebenarnya
            $totalItems = $allContent->count();
            $itemsPerPage = 18; // Set 12 items per page
            $totalPages = ceil($totalItems / $itemsPerPage);

            // Jika halaman yang diminta melebihi total, redirect ke halaman terakhir
            if ($page > $totalPages) {
                return redirect()->route('anime.movies', ['page' => $totalPages]);
            }

            // Ambil konten untuk halaman yang diminta
            $offset = ($page - 1) * $itemsPerPage;
            $pageContent = $allContent->slice($offset, $itemsPerPage)->values();

            return view('anime.anime-movies', [
                'content' => $pageContent,
                'currentPage' => (int) $page,
                'totalPages' => $totalPages,
                'studioName' => 'Anime Movies'
            ]);

        } catch (\Exception $e) {
            \Log::error('Anime movies page error: ' . $e->getMessage());
            abort(404);
        }
    }
} 
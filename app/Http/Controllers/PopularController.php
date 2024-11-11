<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;

class PopularController extends Controller
{
    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function movies()
    {
        try {
            $page = request('page', 1);
            
            // Get all popular movies
            $allPopularMovies = collect();
            $processedIds = []; // Untuk tracking ID yang sudah diproses
            
            // Ambil popular movies (batasi sampai 5 halaman untuk performa)
            for ($i = 1; $i <= 5; $i++) {
                $movies = $this->tmdb->getPopularMoviesWithPagination($i);
                if (!empty($movies['results'])) {
                    // Filter hanya movie yang belum diproses
                    $uniqueMovies = collect($movies['results'])->filter(function($movie) use (&$processedIds) {
                        if (in_array($movie->id, $processedIds)) {
                            return false;
                        }
                        $processedIds[] = $movie->id;
                        return true;
                    });
                    
                    $allPopularMovies = $allPopularMovies->merge($uniqueMovies);
                }
            }

            // Urutkan berdasarkan popularity
            $allContent = $allPopularMovies
                ->sortByDesc('popularity')
                ->values();

            // Hitung total halaman yang sebenarnya
            $totalItems = $allContent->count();
            $itemsPerPage = 18;
            $totalPages = ceil($totalItems / $itemsPerPage);

            // Jika halaman yang diminta melebihi total, redirect ke halaman terakhir
            if ($page > $totalPages) {
                return redirect()->route('popular.movies', ['page' => $totalPages]);
            }

            // Ambil konten untuk halaman yang diminta
            $offset = ($page - 1) * $itemsPerPage;
            $pageContent = $allContent->slice($offset, $itemsPerPage)->values();

            return view('popular.popular-movies', [
                'content' => $pageContent,
                'currentPage' => (int) $page,
                'totalPages' => $totalPages,
                'studioName' => 'Popular Movies'
            ]);

        } catch (\Exception $e) {
            \Log::error('Popular movies page error: ' . $e->getMessage());
            abort(404);
        }
    }

    public function tvShows()
    {
        try {
            $page = request('page', 1);
            
            // Get all popular TV shows
            $allPopularTVShows = collect();
            $processedIds = []; // Untuk tracking ID yang sudah diproses
            
            // Ambil popular TV shows (batasi sampai 5 halaman untuk performa)
            for ($i = 1; $i <= 5; $i++) {
                $shows = $this->tmdb->getPopularTVShowsPagination($i);
                if (!empty($shows['results'])) {
                    // Filter hanya TV shows yang belum diproses
                    $uniqueShows = collect($shows['results'])->filter(function($show) use (&$processedIds) {
                        if (in_array($show->id, $processedIds)) {
                            return false;
                        }
                        $processedIds[] = $show->id;
                        return true;
                    });
                    
                    $allPopularTVShows = $allPopularTVShows->merge($uniqueShows);
                }
            }

            // Urutkan berdasarkan popularity
            $allContent = $allPopularTVShows
                ->sortByDesc('popularity')
                ->values();

            // Hitung total halaman yang sebenarnya
            $totalItems = $allContent->count();
            $itemsPerPage = 12;
            $totalPages = ceil($totalItems / $itemsPerPage);

            // Jika halaman yang diminta melebihi total, redirect ke halaman terakhir
            if ($page > $totalPages) {
                return redirect()->route('popular.tvshows', ['page' => $totalPages]);
            }

            // Ambil konten untuk halaman yang diminta
            $offset = ($page - 1) * $itemsPerPage;
            $pageContent = $allContent->slice($offset, $itemsPerPage)->values();

            return view('popular.popular-tvShows', [
                'content' => $pageContent,
                'currentPage' => (int) $page,
                'totalPages' => $totalPages,
                'studioName' => 'Popular TV Shows'
            ]);

        } catch (\Exception $e) {
            \Log::error('Popular TV shows page error: ' . $e->getMessage());
            abort(404);
        }
    }
} 
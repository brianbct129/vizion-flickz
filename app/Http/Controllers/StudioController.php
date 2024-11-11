<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TMDBService;

class StudioController extends Controller
{
    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function marvel()
    {
        try {
            $page = request('page', 1);
            
            // Get all Marvel movies and TV Shows for better control
            $allMarvelMovies = collect();
            $allMarvelTVShows = collect();
            
            // Ambil semua movie Marvel yang tersedia (batasi sampai 5 halaman untuk performa)
            for ($i = 1; $i <= 5; $i++) {
                $movies = $this->tmdb->getStudioContent(420, $i); // 420 adalah ID Marvel Studios
                if (!empty($movies['results'])) {
                    // Filter: skip item jika salah satu genrenya adalah Documentary
                    $filteredMovies = collect($movies['results'])->reject(function($movie) {
                        return isset($movie->genre_ids) && in_array(99, $movie->genre_ids);
                    });
                    $allMarvelMovies = $allMarvelMovies->merge($filteredMovies);
                }
            }
            
            // Ambil TV Shows Marvel (batasi sampai 5 halaman)
            for ($i = 1; $i <= 5; $i++) {
                $tvShows = $this->tmdb->getMarvelTVShows($i);
                if (!empty($tvShows['results'])) {
                    // Filter: skip item jika salah satu genrenya adalah Documentary
                    $filteredTVShows = collect($tvShows['results'])->reject(function($show) {
                        return isset($show->genre_ids) && in_array(99, $show->genre_ids);
                    });
                    $allMarvelTVShows = $allMarvelTVShows->merge($filteredTVShows);
                }
            }

            // Gabungkan dan urutkan semua konten
            $allContent = $allMarvelMovies->merge($allMarvelTVShows)
                ->sortByDesc('release_date')
                ->values();

            // Hitung total halaman yang sebenarnya
            $totalItems = $allContent->count();
            $itemsPerPage = 12;
            $totalPages = ceil($totalItems / $itemsPerPage);

            // Jika halaman yang diminta melebihi total, redirect ke halaman terakhir
            if ($page > $totalPages) {
                return redirect()->route('studio.marvel', ['page' => $totalPages]);
            }

            // Ambil konten untuk halaman yang diminta
            $offset = ($page - 1) * $itemsPerPage;
            $pageContent = $allContent->slice($offset, $itemsPerPage)->values();

            return view('studio.marvel', [
                'content' => $pageContent,
                'currentPage' => (int) $page,
                'totalPages' => $totalPages,
                'studioName' => 'Marvel Cinematic Universe'
            ]);

        } catch (\Exception $e) {
            \Log::error('Marvel page error: ' . $e->getMessage());
            abort(404);
        }
    }

    public function dc()
    {
        try {
            $page = request('page', 1);
            
            // Get all DC movies and TV Shows for better control
            $allDCMovies = collect();
            $allDCTVShows = collect();
            
            // Ambil semua movie DC yang tersedia (batasi sampai 5 halaman untuk performa)
            for ($i = 1; $i <= 5; $i++) {
                $movies = $this->tmdb->getStudioContent(9993, $i); // 9993 adalah ID DC Studios
                if (!empty($movies['results'])) {
                    $allDCMovies = $allDCMovies->merge($movies['results']);
                }
            }
            
            // Ambil TV Shows DC (batasi sampai 5 halaman)
            for ($i = 1; $i <= 5; $i++) {
                $tvShows = $this->tmdb->getDCTVShows($i);
                if (!empty($tvShows['results'])) {
                    $allDCTVShows = $allDCTVShows->merge($tvShows['results']);
                }
            }

            // Gabungkan dan urutkan semua konten
            $allContent = $allDCMovies->merge($allDCTVShows)
                ->sortByDesc('release_date')
                ->values();

            // Hitung total halaman yang sebenarnya
            $totalItems = $allContent->count();
            $itemsPerPage = 18;
            $totalPages = ceil($totalItems / $itemsPerPage);

            // Jika halaman yang diminta melebihi total, redirect ke halaman terakhir
            if ($page > $totalPages) {
                return redirect()->route('studio.dc', ['page' => $totalPages]);
            }

            // Ambil konten untuk halaman yang diminta
            $offset = ($page - 1) * $itemsPerPage;
            $pageContent = $allContent->slice($offset, $itemsPerPage)->values();

            return view('studio.dc', [
                'content' => $pageContent,
                'currentPage' => (int) $page,
                'totalPages' => $totalPages,
                'studioName' => 'DC Universe'
            ]);

        } catch (\Exception $e) {
            \Log::error('DC page error: ' . $e->getMessage());
            abort(404);
        }
    }
} 
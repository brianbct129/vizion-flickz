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
            
            // Studio IDs untuk Movies
            $movieStudioIds = [420, 7505, 160251]; // Marvel Studios, Marvel Entertainment, Marvel Entertainment Group
            
            // Get Marvel Movies
            foreach ($movieStudioIds as $studioId) {
                for ($i = 1; $i <= 5; $i++) {
                    $movies = $this->tmdb->getStudioContent($studioId, $i);
                    if (!empty($movies['results'])) {
                        $filteredMovies = collect($movies['results'])->reject(function($movie) {
                            return (isset($movie->genre_ids) && in_array(99, $movie->genre_ids)) || 
                                   (isset($movie->vote_average) && $movie->vote_average < 5);
                        });
                        $allMarvelMovies = $allMarvelMovies->merge($filteredMovies);
                    }
                }
            }
            
            // Menghilangkan duplikat movies
            $allMarvelMovies = $allMarvelMovies->unique('id');
            
            // Studio IDs untuk TV Shows
            $tvStudioIds = [420, 38679]; // Marvel Studios dan Marvel Entertainment Group
            
            // Get Marvel TV Shows
            foreach ($tvStudioIds as $studioId) {
                for ($i = 1; $i <= 5; $i++) {
                    $tvShows = $this->tmdb->getMarvelTVShows($studioId, $i);
                    if (!empty($tvShows['results'])) {
                        $filteredTVShows = collect($tvShows['results'])->reject(function($show) {
                            return (isset($show->genre_ids) && in_array(99, $show->genre_ids)) || 
                                   (isset($show->vote_average) && $show->vote_average < 5);
                        });
                        $allMarvelTVShows = $allMarvelTVShows->merge($filteredTVShows);
                    }
                }
            }

            // Menghilangkan duplikat TV Shows
            $allMarvelTVShows = $allMarvelTVShows->unique('id');

            // Gabungkan dan urutkan semua konten
            $allContent = $allMarvelMovies->merge($allMarvelTVShows)
                ->sortByDesc('release_date')
                ->values();

            // Pagination logic
            $totalItems = $allContent->count();
            $itemsPerPage = 12;
            $totalPages = ceil($totalItems / $itemsPerPage);

            if ($page > $totalPages) {
                return redirect()->route('studio.marvel', ['page' => $totalPages]);
            }

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
            
            // Studio IDs untuk Movies
            $movieStudioIds = [128064, 429, 9993]; // DC Studios, DC Entertainment, Warner Bros. Pictures
            
            // Get DC Movies
            foreach ($movieStudioIds as $studioId) {
                for ($i = 1; $i <= 5; $i++) {
                    $movies = $this->tmdb->getStudioContent($studioId, $i);
                    if (!empty($movies['results'])) {
                        $filteredMovies = collect($movies['results'])->reject(function($movie) {
                            return (isset($movie->genre_ids) && in_array(99, $movie->genre_ids)) || 
                                   (isset($movie->vote_average) && $movie->vote_average < 5);
                        });
                        $allDCMovies = $allDCMovies->merge($filteredMovies);
                    }
                }
            }
            
            // Menghilangkan duplikat movies
            $allDCMovies = $allDCMovies->unique('id');
            
            // Studio IDs untuk TV Shows
            $tvStudioIds = [128064, 429, 9993]; // DC Studios dan DC Entertainment
            
            // Get DC TV Shows
            foreach ($tvStudioIds as $studioId) {
                for ($i = 1; $i <= 5; $i++) {
                    $tvShows = $this->tmdb->getDCTVShows($studioId, $i);
                    if (!empty($tvShows['results'])) {
                        $filteredTVShows = collect($tvShows['results'])->reject(function($show) {
                            return (isset($show->genre_ids) && in_array(99, $show->genre_ids)) || 
                                   (isset($show->vote_average) && $show->vote_average < 5);
                        });
                        $allDCTVShows = $allDCTVShows->merge($filteredTVShows);
                    }
                }
            }

            // Menghilangkan duplikat TV Shows
            $allDCTVShows = $allDCTVShows->unique('id');

            // Gabungkan dan urutkan semua konten
            $allContent = $allDCMovies->merge($allDCTVShows)
                ->sortByDesc('release_date')
                ->values();

            // Pagination logic
            $totalItems = $allContent->count();
            $itemsPerPage = 12;
            $totalPages = ceil($totalItems / $itemsPerPage);

            if ($page > $totalPages) {
                return redirect()->route('studio.dc', ['page' => $totalPages]);
            }

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
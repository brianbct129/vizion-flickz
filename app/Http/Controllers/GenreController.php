<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;

class GenreController extends Controller
{
    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function show($id, $name)
    {
        try {
            $page = request('page', 1);
            
            // Collect all content for better pagination
            $allMovies = collect();
            $allTVShows = collect();
            
            // Get movies from multiple pages (limit to 5 pages for performance)
            for ($i = 1; $i <= 5; $i++) {
                $movies = $this->tmdb->getMoviesByGenre($id, $i);
                if (!empty($movies['results'])) {
                    $allMovies = $allMovies->merge($movies['results']);
                }
            }
            
            // Get TV shows from multiple pages
            for ($i = 1; $i <= 5; $i++) {
                $tvShows = $this->tmdb->getTvShowsByGenre($id, $i);
                if (!empty($tvShows['results'])) {
                    $allTVShows = $allTVShows->merge($tvShows['results']);
                }
            }
            
            // Get current genre info
            $currentGenre = collect($this->tmdb->getMenuGenres())
                ->firstWhere('id', $id);

            // Merge all content and sort by popularity
            $allContent = $allMovies->merge($allTVShows)
                ->sortByDesc('popularity')
                ->values();

            // Calculate pagination
            $totalItems = $allContent->count();
            $itemsPerPage = 18;
            $totalPages = ceil($totalItems / $itemsPerPage);

            // Redirect if requested page exceeds total pages
            if ($page > $totalPages && $totalPages > 0) {
                return redirect()->route('genres.show', [
                    'id' => $id,
                    'name' => $name,
                    'page' => $totalPages
                ]);
            }

            // Get content for current page
            $offset = ($page - 1) * $itemsPerPage;
            $pageContent = $allContent->slice($offset, $itemsPerPage)->values();

            return view('genres.show', [
                'content' => $pageContent,
                'currentGenre' => $currentGenre,
                'currentPage' => (int) $page,
                'totalPages' => $totalPages,
                'genreId' => $id,
                'genreName' => $name
            ]);

        } catch (\Exception $e) {
            \Log::error('Genre page error: ' . $e->getMessage());
            abort(404);
        }
    }
} 
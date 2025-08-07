<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use App\Helpers\HashidHelper;

class GenreController extends Controller
{
    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function show($hash, $name)
    {
        try {
            // Decode hash to get real ID
            $id = HashidHelper::decode($hash);
            if (!$id) {
                abort(404);
            }
            
            $page = request('page', 1);
            
            // Get both movie and TV genres
            $movieGenres = collect($this->tmdb->getMenuGenres());
            $tvGenres = collect($this->tmdb->getTvGenres());
            
            // Find genre in either movie or TV genres
            $currentGenre = $movieGenres->firstWhere('id', (int)$id) ?? 
                           $tvGenres->firstWhere('id', (int)$id);

            if (!$currentGenre) {
                abort(404, 'Genre not found');
            }

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
                'genres' => $movieGenres->merge($tvGenres)->unique('id'), // Merge both genre types
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
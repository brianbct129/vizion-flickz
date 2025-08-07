<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use App\Helpers\HashidHelper;
use Illuminate\Support\Facades\Cache;

class MovieController extends Controller
{
    protected $tmdb;
    protected $cache_ttl = 3600; // 1 hour cache

    public function __construct()
    {
        $this->tmdb = new TMDBService();
    }

    public function index()
    {
        $bannerData = $this->tmdb->getPopularMoviesForBanner();
        $featuredMovies = $this->tmdb->getTrendingMovies();
        $animeList = $this->tmdb->getPopularAnime();
        $popularData = $this->tmdb->getPopularAll();
        $popularMovies = $this->tmdb->getPopularMovies();
        
        return view('index', [
            'bannerMovies' => $bannerData['movies'],
            'bannerLimit' => $bannerData['limit'],
            'featured' => $featuredMovies,
            'animeList' => $animeList,
            'popular' => $popularData['popular'],
            'popularMovies' => $popularMovies
        ]);
    }

    public function show($hash)
    {
        try {
            // Decode hash to get real ID
            $id = HashidHelper::decode($hash);
            if (!$id) {
                abort(404);
            }
            
            // Get movie details with credits and similar movies
            $movie = $this->tmdb->getMovieDetails($id, [
                'append_to_response' => 'credits,similar,videos'
            ]);

            if (!$movie) {
                abort(404);
            }

            // Get similar movies with filters
            $popularMovies = collect($this->tmdb->getPopularMovies()['popular'] ?? [])
                ->filter(function($movie) {
                    if (empty($movie->poster_path) || empty($movie->release_date)) {
                        return false;
                    }
                    
                    $releaseDate = \Carbon\Carbon::parse($movie->release_date);
                    
                    // Filter tahun, bahasa, dan rating
                    return $releaseDate->year >= 1999 && 
                           $movie->original_language === 'en' && 
                           ($movie->vote_average ?? 0) >= 5.0;
                })
                ->unique('id')
                ->shuffle()
                ->take(6)
                ->values();

            // Get trailer
            $trailer = collect($movie->videos->results ?? [])
                ->first(function($video) {
                    return $video->type === 'Trailer' && $video->site === 'YouTube';
                });

            // Get cast with profile images only
            $cast = collect($movie->credits->cast ?? [])
                ->filter(fn($actor) => !empty($actor->profile_path))
                ->take(9)
                ->values();

            // Jika cast kosong, gunakan crew
            $crew = collect($movie->credits->crew ?? [])
                ->take(10)
                ->values();

            return view('movies', [
                'movie' => $movie,
                'popularMovies' => $popularMovies,
                'genres' => $movie->genres ?? [],
                'videos' => $movie->videos->results ?? [],
                'trailer' => $trailer,
                'cast' => $cast,
                'crew' => $crew
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading movie: ' . $e->getMessage());
            abort(404);
        }
    }
}

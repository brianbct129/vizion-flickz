<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\TMDBService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class ShareGenresData
{
    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function handle(Request $request, Closure $next)
    {
        // Cache genres untuk 24 jam
        $genres = Cache::remember('tmdb_genres', 60*60*24, function () {
            return $this->tmdb->getMovieGenres();
        });

        // Share dengan semua view
        View::share('genres', $genres);

        return $next($request);
    }
}

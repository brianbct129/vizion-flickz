<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\TMDBService;
use Illuminate\Support\Facades\Cache;

class GenreComposer
{
    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function compose(View $view)
    {
        // Cache genres untuk 24 jam
        $genres = Cache::remember('tmdb_genres', 60*60*24, function () {
            return $this->tmdb->getMovieGenres();
        });

        $view->with('genres', $genres);
    }
} 
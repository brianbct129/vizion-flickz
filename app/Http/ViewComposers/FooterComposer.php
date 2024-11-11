<?php

namespace App\Http\ViewComposers;

use App\Services\TMDBService;
use Illuminate\View\View;

class FooterComposer
{
    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function compose(View $view)
    {
        $genres = $this->tmdb->getPopularGenres();
        $view->with('footerGenres', $genres);
    }
} 
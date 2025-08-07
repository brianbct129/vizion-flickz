<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\PopularController;
use App\Http\Controllers\DrakorController;
use App\Http\Controllers\AnimeController;

Route::get('/', [MovieController::class, 'index'])->name('home');
Route::get('/tv/{hash}/season/{season}/episode/{episode}', [TvShowController::class, 'episode'])
    ->name('tv.episode')
    ->where('hash', '[a-zA-Z0-9]+')
    ->middleware('cache.headers:public;max_age=3600');
Route::get('/movie/{hash}', [MovieController::class, 'show'])
    ->name('movies.show')
    ->where('hash', '[a-zA-Z0-9]+')
    ->middleware('cache.headers:public;max_age=3600');
Route::get('/tv/{hash}', [TvShowController::class, 'show'])
    ->name('tv.show')
    ->where('hash', '[a-zA-Z0-9]+');
Route::get('/tv/{hash}/season/{season}', [TvShowController::class, 'season'])
    ->name('tv.season')
    ->where('hash', '[a-zA-Z0-9]+');
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/genre/{hash}', [GenreController::class, 'show'])
    ->name('genres.show')
    ->where('hash', '[a-zA-Z0-9]+');
Route::get('/genre/{hash}/{name}', [GenreController::class, 'show'])
    ->name('genres.show')
    ->where('hash', '[a-zA-Z0-9]+');
Route::get('/studio/marvel', [StudioController::class, 'marvel'])->name('studio.marvel');
Route::get('/studio/dc', [StudioController::class, 'dc'])->name('studio.dc');
Route::get('/popular/movies', [PopularController::class, 'movies'])->name('popular.movies');
Route::get('/popular/shows', [PopularController::class, 'tvShows'])->name('popular.tvshows');
Route::get('/kdrama', [DrakorController::class, 'index'])->name('drakor.index');
Route::get('/anime/popular', [AnimeController::class, 'popular'])->name('anime.popular');
Route::get('/anime/movies', [AnimeController::class, 'animeMovies'])->name('anime.movies');






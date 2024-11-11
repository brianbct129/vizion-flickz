<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

abstract class Controller
{
    protected $tmdbBaseUrl = 'https://api.themoviedb.org/3';
    protected $tmdbApiKey;

    public function __construct()
    {
        $this->tmdbApiKey = config('services.tmdb.api_key');
    }

    protected function getTmdbData($endpoint, $params = [])
    {
        $response = Http::get($this->tmdbBaseUrl . $endpoint, array_merge([
            'api_key' => $this->tmdbApiKey,
        ], $params));

        return $response->json();
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TMDBService;
use GuzzleHttp\Client;

class TMDBServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(TMDBService::class, function ($app) {
            dd([
                'token' => env('TMDB_READ_ACCESS_TOKEN'),
                'base_url' => 'https://api.themoviedb.org/3/'
            ]);

            $client = new Client([
                'base_uri' => 'https://api.themoviedb.org/3/',
                'headers' => [
                    'Authorization' => 'Bearer ' . env('TMDB_READ_ACCESS_TOKEN'),
                    'accept' => 'application/json',
                ]
            ]);
            
            return new TMDBService($client);
        });
    }
} 
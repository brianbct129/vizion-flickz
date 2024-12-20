<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TMDBService
{
    protected $client;
    protected $cache_ttl = 3600; // 1 hour cache
    
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.themoviedb.org/3/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('TMDB_READ_ACCESS_TOKEN'),
                'accept' => 'application/json',
            ]
        ]);
    }

    public function getPopularMoviesForBanner()
    {
        try {
            $response = $this->client->request('GET', 'movie/popular', [
                'query' => [
                    'language' => 'en-US',
                    'page' => 1,
                    'include_adult' => false,
                    'vote_count.gte' => 50
                ]
            ]);

            $result = json_decode($response->getBody());
            
            // Filter dan format movies untuk banner
            $movies = collect($result->results)
                ->filter(function($movie) {
                    return !empty($movie->poster_path) && 
                           !empty($movie->backdrop_path) && 
                           !empty($movie->release_date) &&
                           !empty($movie->overview) &&
                           $movie->original_language === 'en' &&
                           (!isset($movie->genre_ids) || !in_array(99, $movie->genre_ids));
                })
                ->take(5)  // Ambil 5 film untuk banner
                ->values();

            return [
                'movies' => $movies,
                'limit' => 5
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB Banner Movies Error: ' . $e->getMessage());
            return [
                'movies' => collect([]),
                'limit' => 5
            ];
        }
    }

    public function getPopularMoviesWithPagination($page = 1)
    {
        try {
            $response = $this->client->request('GET', 'movie/popular', [
                'query' => [
                    'language' => 'en-US',
                    'page' => $page,
                    'include_adult' => false,
                    'vote_count.gte' => 50
                ]
            ]);

            $result = json_decode($response->getBody());
            $currentDate = now()->format('Y-m-d'); // Mendapatkan tanggal hari ini
            
            return [
                'results' => collect($result->results)
                    ->filter(function($movie) use ($currentDate) {
                        return !empty($movie->poster_path) && 
                               !empty($movie->release_date) &&
                               $movie->original_language === 'en' &&
                               (!isset($movie->genre_ids) || !in_array(99, $movie->genre_ids)) &&
                               $movie->release_date <= $currentDate; // Filter film yang sudah rilis
                    })
                    ->map(function($movie) {
                        return (object)[
                            'id' => $movie->id,
                            'title' => $movie->title,
                            'poster_path' => $movie->poster_path,
                            'release_date' => $movie->release_date,
                            'media_type' => 'movie',
                            'vote_average' => $movie->vote_average,
                            'popularity' => $movie->popularity,
                            'genre_ids' => $movie->genre_ids ?? []
                        ];
                    })
                    ->values(),
                'total_pages' => $result->total_pages,
                'current_page' => $result->page
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB Popular Movies Pagination Error: ' . $e->getMessage());
            return [
                'results' => collect([]),
                'total_pages' => 0,
                'current_page' => 1
            ];
        }
    }

    public function getTrendingMovies($limit = 12)
    {
        try {
            $response = $this->client->request('GET', 'trending/movie/week', [
                'query' => [
                    'language' => 'en-US'
                ]
            ]);

            $data = json_decode($response->getBody());
            return array_slice($data->results, 0, $limit);
        } catch (\Exception $e) {
            \Log::error('TMDB API Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getPopularAnime($limit = 12)
    {
        try {
            $currentDate = now()->format('Y-m-d');
            $startDate = '1999-01-01'; // Mulai dari tahun One Piece
            
            // Get Currently Airing Anime TV Shows
            $tvResponse = $this->client->request('GET', 'discover/tv', [
                'query' => [
                    'language' => 'en-US',
                    'sort_by' => 'popularity.desc',
                    'with_genres' => '16', // Animation genre
                    'with_original_language' => 'ja', // Japanese content
                    'with_keywords' => '210024|287501|6075', // Anime keywords
                    'first_air_date.gte' => $startDate,
                    'first_air_date.lte' => $currentDate,
                    'page' => 1,
                    'include_adult' => false,
                    'vote_count.gte' => 100 // Memastikan anime yang populer
                ]
            ]);

            $tvData = json_decode($tvResponse->getBody())->results;
            
            // Debug: Log raw data
            \Log::info('Raw Anime Data:', ['data' => $tvData]);
            
            // Filter basic requirements only
            $filteredAnime = collect($tvData)
                ->filter(function($show) {
                    return !empty($show->poster_path) && 
                           $show->original_language === 'ja' &&
                           !empty($show->first_air_date);
                })
                ->map(function($show) {
                    $show->media_type = 'tv';
                    return $show;
                })
                ->sortByDesc('popularity')
                ->values()
                ->take($limit)
                ->all();

            // Debug: Log filtered data
            \Log::info('Filtered Anime:', ['filtered' => $filteredAnime]);

            return $filteredAnime;

        } catch (\Exception $e) {
            \Log::error('TMDB API Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getPopularAnimePagination($page = 1)
    {
        try {
            $currentDate = now()->format('Y-m-d');
            $startDate = '1999-01-01'; // Mulai dari tahun One Piece
            $response = $this->client->request('GET', 'discover/tv', [
                'query' => [
                    'language' => 'en-US',
                    'sort_by' => 'popularity.desc',
                    'with_genres' => '16', // Animation genre
                    'with_original_language' => 'ja', // Japanese content
                    'with_keywords' => '210024|287501|6075', // Anime keywords
                    'page' => $page,
                    'first_air_date.gte' => $startDate,
                    'first_air_date.lte' => $currentDate,
                    'include_adult' => false,
                    'vote_count.gte' => 100 // Memastikan anime yang populer
                ]
            ]);
    
            $result = json_decode($response->getBody());
            
            return [
                'results' => collect($result->results)
                    ->filter(function($show) {
                        return !empty($show->poster_path) && 
                               !empty($show->first_air_date) &&
                               $show->vote_average >= 5.0 && 
                               $show->vote_average <= 10.0 &&
                               $show->vote_count >= 50;
                    })
                    ->map(function($show) {
                        return (object)[
                            'id' => $show->id,
                            'title' => $show->name,
                            'poster_path' => $show->poster_path,
                            'release_date' => $show->first_air_date,
                            'media_type' => 'tv',
                            'vote_average' => $show->vote_average,
                            'popularity' => $show->popularity,
                            'genre_ids' => $show->genre_ids ?? []
                        ];
                    })
                    ->values(),
                'total_pages' => $result->total_pages,
                'current_page' => $result->page
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB Popular Anime Error: ' . $e->getMessage());
            return ['results' => collect([]), 'total_pages' => 0, 'current_page' => 1];
        }
    }

    public function getPopularAll($limit = 4)
    {
        try {
            $response = $this->client->request('GET', 'trending/all/week', [
                'query' => [
                    'language' => 'en-US',
                    'page' => 1,
                    'sort_by' => 'vote_count.desc', // Sort berdasarkan jumlah vote
                    'vote_count.gte' => 1000, // Minimal vote count
                    'vote_average.gte' => 5.0, // Minimal rating
                    'include_adult' => false,
                    'with_original_language' => 'en|ko|ja'
                ]
            ]);

            $data = json_decode($response->getBody());
            
            // Filter dan sort berdasarkan vote_count
            $filteredResults = collect($data->results)
                ->filter(function($item) {
                    // Basic validation
                    if (empty($item->poster_path)) {
                        return false;
                    }

                    // Filter untuk konten dari US, JP, atau KR saja
                    if (isset($item->origin_country)) {
                        $allowedCountries = ['US', 'JP', 'KR'];
                        $hasAllowedCountry = false;
                        foreach ($item->origin_country as $country) {
                            if (in_array($country, $allowedCountries)) {
                                $hasAllowedCountry = true;
                                break;
                            }
                        }
                        if (!$hasAllowedCountry) {
                            return false;
                        }
                    }

                    // Pastikan memiliki vote count yang cukup
                    return isset($item->vote_count) && $item->vote_count >= 1000;
                })
                ->sortByDesc('vote_count')
                ->take($limit)
                ->map(function($item) {
                    // Tambahkan type berdasarkan media_type
                    $item->type = $item->media_type ?? 'movie';
                    return $item;
                })
                ->values()
                ->all();
            
            return [
                'popular' => $filteredResults,
                'limit' => $limit
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB API Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getPopularMovies()
    {
        try {
            $response = $this->client->request('GET', 'trending/all/week', [
                'query' => [
                    'language' => 'en-US',
                    'page' => 1
                ]
            ]);

            $data = json_decode($response->getBody());
            
            // Tambahkan informasi "type" untuk membedakan
            foreach ($data->results as $item) {
                $item->type = 'popular';
            }
            
            return [
                'popular' => $data->results
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB API Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getTvShowDetails($id, $options = [])
    {
        $cacheKey = "tv_show_{$id}_" . md5(serialize($options));
        
        return Cache::remember($cacheKey, $this->cache_ttl, function () use ($id, $options) {
            try {
                $defaultOptions = [
                    'language' => 'en-US'
                ];

                $query = array_merge($defaultOptions, $options);
                $response = $this->client->request('GET', "tv/{$id}", [
                    'query' => $query
                ]);

                return json_decode($response->getBody());
            } catch (\Exception $e) {
                \Log::error('TMDB API Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    public function getTvShowSeasons($id, $season_number)
    {
        $cacheKey = "tv_show_{$id}_season_{$season_number}";
        
        return Cache::remember($cacheKey, $this->cache_ttl, function () use ($id, $season_number) {
            try {
                $response = $this->client->request('GET', "tv/{$id}/season/{$season_number}", [
                    'query' => [
                        'language' => 'en-US'
                    ]
                ]);

                return json_decode($response->getBody());
            } catch (\Exception $e) {
                \Log::error('TMDB API Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    public function getEpisodeDetails($tv_id, $season_number, $episode_number)
    {
        try {
            // Get Episode details
            $response = $this->client->request('GET', "tv/{$tv_id}/season/{$season_number}/episode/{$episode_number}", [
                'query' => [
                    'language' => 'en-US',
                    'append_to_response' => 'credits,videos'
                ]
            ]);

            return json_decode($response->getBody());
        } catch (\Exception $e) {
            \Log::error('TMDB API Error: ' . $e->getMessage());
            return null;
        }
    }

    public function getMovieDetails($id, $options = [])
    {
        $cacheKey = "movie_{$id}_" . md5(serialize($options));
        
        return Cache::remember($cacheKey, $this->cache_ttl, function () use ($id, $options) {
            try {
                $defaultOptions = [
                    'language' => 'en-US'
                ];

                $query = array_merge($defaultOptions, $options);
                $response = $this->client->request('GET', "movie/{$id}", [
                    'query' => $query
                ]);

                return json_decode($response->getBody());
            } catch (\Exception $e) {
                \Log::error('TMDB API Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    public function multiSearch($query, $page = 1)
    {
        try {
            $response = $this->client->request('GET', 'search/multi', [
                'query' => [
                    'query' => $query,
                    'language' => 'en-US',
                    'page' => $page,
                    'include_adult' => true,
                    'vote_count.gte' => 50,
                    'vote_average.gte' => 5.0,
                    'primary_release_date.gte' => '1999-01-01',
                    'primary_release_date.lte' => now()->format('Y-m-d'),
                    'first_air_date.gte' => '1999-01-01',
                    'first_air_date.lte' => now()->format('Y-m-d')
                ]
            ]);

            $data = json_decode($response->getBody())->results;
            
            return collect($data)
                ->filter(function($item) {
                    // Basic filters
                    if (!in_array($item->media_type, ['movie', 'tv']) || 
                        empty($item->poster_path) || 
                        ($item->original_language ?? '') === 'id' || 
                        ($item->vote_average ?? 0) < 5.0) {
                        return false;
                    }

                    // Check release date
                    $releaseDate = $item->media_type === 'movie' ? 
                                  ($item->release_date ?? null) : 
                                  ($item->first_air_date ?? null);
                    
                    if (!$releaseDate || \Carbon\Carbon::parse($releaseDate)->year < 1999) {
                        return false;
                    }

                    // Check excluded genres
                    if (isset($item->genre_ids) && 
                        !empty(array_intersect($item->genre_ids, [99, 10764, 10767, 10763, 10766]))) {
                        return false;
                    }

                    // Check excluded keywords in title
                    $title = strtolower($item->media_type === 'movie' ? $item->title : $item->name);
                    $excludedWords = ['tonight show', 'late show', 'talk show', 'av ', 'jav', 'adult'];
                    
                    return !Str::contains($title, $excludedWords);
                })
                ->map(function($item) {
                    $type = $item->media_type;
                    
                    // Determine content type
                    if ($item->media_type === 'tv') {
                        if ($item->original_language === 'ja') {
                            $type = isset($item->genre_ids) && in_array(16, $item->genre_ids) ? 'anime' : 'jdrama';
                        } elseif ($item->original_language === 'ko') {
                            $type = 'kdrama';
                        }
                    }

                    return (object)[
                        'id' => $item->id,
                        'title' => $item->media_type === 'movie' ? $item->title : $item->name,
                        'poster_path' => $item->poster_path,
                        'media_type' => $type,
                        'release_date' => $item->media_type === 'movie' ? 
                                        $item->release_date ?? null : 
                                        $item->first_air_date ?? null,
                        'vote_average' => $item->vote_average ?? null
                    ];
                })
                ->sortByDesc('vote_average')
                ->values()
                ->all();
                
        } catch (\Exception $e) {
            \Log::error('TMDB Search Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getMenuGenres()
    {
        try {
            $response = $this->client->request('GET', 'genre/movie/list', [
                'query' => [
                    'language' => 'en-US'
                ]
            ]);

            return json_decode($response->getBody())->genres;
        } catch (\Exception $e) {
            \Log::error('TMDB Genre Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getTvGenres()
    {
        try {
            $response = $this->client->request('GET', 'genre/tv/list', [
                'query' => [
                    'language' => 'en-US'
                ]
            ]);

            return json_decode($response->getBody())->genres;
        } catch (\Exception $e) {
            \Log::error('TMDB TV Genre Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getMoviesByGenre($genreId, $page = 1)
    {
        try {
            $response = $this->client->request('GET', 'discover/movie', [
                'query' => [
                    'with_genres' => $genreId,
                    'language' => 'en-US',
                    'sort_by' => 'popularity.desc',
                    'include_adult' => false,
                    'vote_count.gte' => 50,
                    'page' => $page
                ]
            ]);

            $result = json_decode($response->getBody());
            
            return [
                'results' => collect($result->results)
                    ->filter(function($movie) {
                        return !empty($movie->poster_path) && 
                               !empty($movie->release_date) && 
                               $movie->original_language === 'en';
                    })
                    ->take(24)  // Ambil 24 movies
                    ->values(),
                'total_pages' => $result->total_pages,
                'current_page' => $result->page
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB Movies by Genre Error: ' . $e->getMessage());
            return ['results' => collect([]), 'total_pages' => 0, 'current_page' => 1];
        }
    }

    public function getTvShowsByGenre($genreId, $page = 1)
    {
        try {
            $response = $this->client->request('GET', 'discover/tv', [
                'query' => [
                    'with_genres' => $genreId,
                    'language' => 'en-US',
                    'sort_by' => 'popularity.desc',
                    'include_adult' => false,
                    'vote_count.gte' => 50,
                    'page' => $page
                ]
            ]);

            $result = json_decode($response->getBody());
            
            return [
                'results' => collect($result->results)
                    ->filter(function($show) {
                        return !empty($show->poster_path) && 
                               !empty($show->first_air_date) && 
                               in_array($show->original_language, ['en', 'ja', 'ko']);
                    })
                    ->take(24)  // Ambil 24 TV shows
                    ->values(),
                'total_pages' => $result->total_pages,
                'current_page' => $result->page
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB TV Shows by Genre Error: ' . $e->getMessage());
            return ['results' => collect([]), 'total_pages' => 0, 'current_page' => 1];
        }
    }

    public function getStudioContent($studioId, $page = 1)
    {
        try {
            $response = $this->client->request('GET', 'discover/movie', [
                'query' => [
                    'with_companies' => $studioId,
                    'language' => 'en-US',
                    'sort_by' => 'primary_release_date.desc',
                    'include_adult' => false,
                    'vote_count.gte' => 50,
                    'page' => $page
                ]
            ]);

            $result = json_decode($response->getBody());
            
            return [
                'results' => collect($result->results)
                    ->filter(function($movie) {
                        return !empty($movie->poster_path) && 
                               !empty($movie->release_date) && 
                               $movie->original_language === 'en' &&
                               (!isset($movie->genre_ids) || !in_array(99, $movie->genre_ids)); // Filter Documentary
                    })
                    ->map(function($movie) {
                        return (object)[
                            'id' => $movie->id,
                            'title' => $movie->title,
                            'poster_path' => $movie->poster_path,
                            'release_date' => $movie->release_date,
                            'media_type' => 'movie',
                            'vote_average' => $movie->vote_average,
                            'popularity' => $movie->popularity,
                            'genre_ids' => $movie->genre_ids ?? [] // Pastikan genre_ids tetap ada
                        ];
                    })
                    ->values(),
                'total_pages' => $result->total_pages,
                'current_page' => $result->page
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB Studio Content Error: ' . $e->getMessage());
            return ['results' => collect([]), 'total_pages' => 0, 'current_page' => 1];
        }
    }

    public function getMovieGenres()
    {
        try {
            $response = $this->client->request('GET', 'genre/movie/list', [
                'query' => [
                    'language' => 'en-US'
                ]
            ]);

            return collect(json_decode($response->getBody())->genres)
                ->sortBy('name')
                ->values();
            
        } catch (\Exception $e) {
            \Log::error('TMDB Movie Genres Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function getMarvelTVShows($studioId, $page = 1)
    {
        try {
            $response = $this->client->request('GET', 'discover/tv', [
                'query' => [
                    'with_companies' => $studioId,
                    'language' => 'en-US',
                    'sort_by' => 'first_air_date.desc',
                    'include_adult' => false,
                    'vote_count.gte' => 50,
                    'vote_average.gte' => 5.0,
                    'first_air_date.gte' => '2013-01-01',
                    'page' => $page
                ]
            ]);

            $result = json_decode($response->getBody());
            
            return [
                'results' => collect($result->results)
                    ->filter(function($show) {
                        return !empty($show->poster_path) && 
                               !empty($show->first_air_date) &&
                               $show->original_language === 'en' &&
                               substr($show->first_air_date, 0, 4) >= 2013 &&
                               (!isset($show->genre_ids) || !in_array(99, $show->genre_ids));
                    })
                    ->map(function($show) {
                        return (object)[
                            'id' => $show->id,
                            'title' => $show->name,
                            'poster_path' => $show->poster_path,
                            'release_date' => $show->first_air_date,
                            'media_type' => 'tv',
                            'vote_average' => $show->vote_average,
                            'popularity' => $show->popularity,
                            'genre_ids' => $show->genre_ids ?? []
                        ];
                    })
                    ->values(),
                'total_pages' => $result->total_pages,
                'current_page' => $result->page
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB Marvel TV Shows Error: ' . $e->getMessage());
            return ['results' => collect([]), 'total_pages' => 0, 'current_page' => 1];
        }
    }

    public function getDCTVShows($studioId, $page = 1)
    {
        try {
            $response = $this->client->request('GET', 'discover/tv', [
                'query' => [
                    'with_companies' => $studioId,
                    'language' => 'en-US',
                    'sort_by' => 'first_air_date.desc',
                    'include_adult' => false,
                    'vote_count.gte' => 50,
                    'vote_average.gte' => 5.0,
                    'first_air_date.gte' => '2013-01-01',
                    'page' => $page
                ]
            ]);

            $result = json_decode($response->getBody());
            
            return [
                'results' => collect($result->results)
                    ->filter(function($show) {
                        return !empty($show->poster_path) && 
                               !empty($show->first_air_date) &&
                               $show->original_language === 'en' &&
                               substr($show->first_air_date, 0, 4) >= 2013 &&
                               (!isset($show->genre_ids) || !in_array(99, $show->genre_ids));
                    })
                    ->map(function($show) {
                        return (object)[
                            'id' => $show->id,
                            'title' => $show->name,
                            'poster_path' => $show->poster_path,
                            'release_date' => $show->first_air_date,
                            'media_type' => 'tv',
                            'vote_average' => $show->vote_average,
                            'popularity' => $show->popularity,
                            'genre_ids' => $show->genre_ids ?? []
                        ];
                    })
                    ->values(),
                'total_pages' => $result->total_pages,
                'current_page' => $result->page
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB DC TV Shows Error: ' . $e->getMessage());
            return ['results' => collect([]), 'total_pages' => 0, 'current_page' => 1];
        }
    }

    public function getPopularTVShowsPagination($page = 1, $endpoint = 'trending/tv/week')
    {
        try {
            $query = [
                'language' => 'en-US',
                'page' => $page,
                'include_adult' => false,
                'vote_average.gte' => 5.0,
                'sort_by' => 'vote_count.desc',
                'vote_count.gte' => 1000,
                'with_original_language' => 'en|ko|ja',
                'with_status' => 'Released|Returning Series',
                'with_type' => 'Scripted|Animation'
            ];

            $response = $this->client->request('GET', $endpoint, ['query' => $query]);
            $result = json_decode($response->getBody());

            // Genre yang akan diexclude
            $excludedGenres = [99, 10764, 10767, 10763, 10766]; 
            $excludedKeywords = [
                'tonight show', 'late show', 'late late show', 
                'daily show', 'talk show', 'live with', 
                'tonight with', 'late night with', 'morning show'
            ];

            return [
                'results' => collect($result->results)
                    ->filter(function($show) use ($excludedGenres, $excludedKeywords) {
                        // Basic filters
                        if (empty($show->poster_path) || empty($show->first_air_date)) {
                            return false;
                        }

                        // Filter untuk show dari US, JP, atau KR saja
                        if (!isset($show->origin_country) || empty($show->origin_country)) {
                            return false;
                        }

                        // Country validation
                        $allowedCountries = ['US', 'JP', 'KR'];
                        $originCountries = $show->origin_country;
                        
                        foreach ($originCountries as $country) {
                            if (!in_array($country, $allowedCountries)) {
                                return false;
                            }
                        }
                        
                        $hasAllowedCountry = false;
                        foreach ($allowedCountries as $country) {
                            if (in_array($country, $originCountries)) {
                                $hasAllowedCountry = true;
                                break;
                            }
                        }
                        if (!$hasAllowedCountry) {
                            return false;
                        }

                        // Genre filter
                        if (!empty($show->genre_ids)) {
                            $hasExcludedGenre = collect($show->genre_ids)
                                ->intersect($excludedGenres)
                                ->isNotEmpty();
                            if ($hasExcludedGenre) {
                                return false;
                            }
                        }

                        // Keyword filter (hanya untuk US shows)
                        if (in_array('US', $show->origin_country)) {
                            $showName = strtolower($show->name);
                            foreach ($excludedKeywords as $keyword) {
                                if (str_contains($showName, $keyword)) {
                                    return false;
                                }
                            }
                        }

                        return $show->vote_average > 5.0;
                    })
                    ->map(function($show) {
                        return (object)[
                            'id' => $show->id,
                            'title' => $show->name,
                            'poster_path' => $show->poster_path,
                            'release_date' => $show->first_air_date,
                            'media_type' => 'tv',
                            'vote_average' => $show->vote_average,
                            'popularity' => $show->popularity,
                            'vote_count' => $show->vote_count ?? 0,
                            'genre_ids' => $show->genre_ids ?? [],
                            'origin_country' => $show->origin_country ?? [],
                            'original_language' => $show->original_language ?? null
                        ];
                    })
                    ->sortByDesc('vote_count')
                    ->values(),
                'total_pages' => $result->total_pages,
                'current_page' => $result->page
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB TV Shows Error: ' . $e->getMessage());
            return ['results' => collect([]), 'total_pages' => 0, 'current_page' => 1];
        }
    }

    public function getKoreanDramas($page = 1)
    {
        try {
            $response = $this->client->request('GET', 'discover/tv', [
                'query' => [
                    'language' => 'en-US',
                    'page' => $page,
                    'include_adult' => false,
                    'vote_count.gte' => 50,
                    'vote_average.gte' => 5.0,
                    'vote_average.lte' => 10.0,
                    'with_original_language' => 'ko', // Korean language
                    'sort_by' => 'popularity.desc'
                ]
            ]);

            $result = json_decode($response->getBody());
            
            // Genre IDs yang akan diexclude
            $excludedGenres = [99, 10764, 10767, 10763, 10766];
            
            return [
                'results' => collect($result->results)
                    ->filter(function($show) use ($excludedGenres) {
                        // Cek apakah show memiliki genre yang diexclude
                        $hasExcludedGenre = isset($show->genre_ids) && 
                                          count(array_intersect($show->genre_ids, $excludedGenres)) > 0;
                        
                        return !empty($show->poster_path) && 
                               !empty($show->first_air_date) &&
                               $show->vote_average >= 5.0 && 
                               $show->vote_average <= 10.0 &&
                               !$hasExcludedGenre &&
                               $show->vote_count >= 50;
                    })
                    ->map(function($show) {
                        return (object)[
                            'id' => $show->id,
                            'title' => $show->name,
                            'poster_path' => $show->poster_path,
                            'release_date' => $show->first_air_date,
                            'media_type' => 'tv',
                            'vote_average' => $show->vote_average,
                            'popularity' => $show->popularity,
                            'genre_ids' => $show->genre_ids ?? []
                        ];
                    })
                    ->values(),
                'total_pages' => $result->total_pages,
                'current_page' => $result->page
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB Korean Dramas Error: ' . $e->getMessage());
            return ['results' => collect([]), 'total_pages' => 0, 'current_page' => 1];
        }
    }

    public function getAnimeMovies($page = 1)
    {
        try {
            $currentDate = now()->format('Y-m-d');
            $startDate = '1999-01-01'; // Mulai dari tahun One Piece
            $response = $this->client->request('GET', 'discover/movie', [
                'query' => [
                    'language' => 'en-US',
                    'sort_by' => 'popularity.desc',
                    'with_genres' => '16', // Animation genre
                    'with_original_language' => 'ja', // Japanese content
                    'first_air_date.gte' => $startDate,
                    'first_air_date.lte' => $currentDate,
                    'page' => $page, // Use the page parameter passed to the method
                    'include_adult' => false,
                    'vote_count.gte' => 100 // Memastikan anime yang populer
                ]
            ]);

            $result = json_decode($response->getBody());
            
            return [
                'results' => collect($result->results)
                    ->filter(function($show) {
                        return !empty($show->poster_path) && 
                               !empty($show->release_date) &&
                               $show->vote_average >= 5.0 && 
                               $show->vote_average <= 10.0 &&
                               $show->vote_count >= 50;
                    })
                    ->map(function($show) {
                        return (object)[
                            'id' => $show->id,
                            'title' => $show->title,
                            'poster_path' => $show->poster_path,
                            'release_date' => $show->release_date,
                            'media_type' => 'movie',
                            'vote_average' => $show->vote_average,
                            'popularity' => $show->popularity,
                            'genre_ids' => $show->genre_ids ?? []
                        ];
                    })
                    ->values(),
                'total_pages' => $result->total_pages,
                'current_page' => $result->page
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB Anime Movies Error: ' . $e->getMessage());
            return ['results' => collect([]), 'total_pages' => 0, 'current_page' => 1];
        }
    }

    public function getPopularGenres($limit = 5)
    {
        try {
            $response = $this->client->request('GET', 'genre/movie/list', [
                'query' => [
                    'language' => 'en-US'
                ]
            ]);

            $genres = json_decode($response->getBody())->genres;
            
            // Ambil 5 genre populer
            $popularGenreIds = [28, 12, 35, 18, 878]; // Action, Adventure, Comedy, Drama, Sci-Fi
            
            return collect($genres)
                ->filter(function($genre) use ($popularGenreIds) {
                    return in_array($genre->id, $popularGenreIds);
                })
                ->take($limit)
                ->values()
                ->all();
                
        } catch (\Exception $e) {
            \Log::error('TMDB Genre Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getPopularTVShows($endpoint = 'trending/tv/week')
    {
        try {
            $query = [
                'language' => 'en-US',
                'include_adult' => false,
                'vote_average.gte' => 5.0,
                'sort_by' => 'vote_count.desc',
                'vote_count.gte' => 1000,
                'with_original_language' => 'en|ko|ja',
                'with_status' => 'Released|Returning Series',
                'with_type' => 'Scripted|Animation'
            ];

            // Get first page to know total pages
            $query['page'] = 1;
            $response = $this->client->request('GET', $endpoint, ['query' => $query]);
            $firstResult = json_decode($response->getBody());
            $totalPages = min($firstResult->total_pages, 5); // Limit to 5 pages
            
            $allResults = collect($firstResult->results);

            // Fetch remaining pages
            for ($page = 2; $page <= $totalPages; $page++) {
                $query['page'] = $page;
                $response = $this->client->request('GET', $endpoint, ['query' => $query]);
                $pageResult = json_decode($response->getBody());
                $allResults = $allResults->concat($pageResult->results);
            }

            // Genre yang akan diexclude
            $excludedGenres = [99, 10764, 10767, 10763, 10766]; 
            $excludedKeywords = [
                'tonight show', 'late show', 'late late show', 
                'daily show', 'talk show', 'live with', 
                'tonight with', 'late night with', 'morning show'
            ];

            return [
                'results' => $allResults
                    ->filter(function($show) use ($excludedGenres, $excludedKeywords) {
                        // Basic filters
                        if (empty($show->poster_path) || empty($show->first_air_date)) {
                            return false;
                        }

                        // Filter untuk show dari US, JP, atau KR saja
                        if (!isset($show->origin_country) || empty($show->origin_country)) {
                            return false;
                        }

                        // Country validation
                        $allowedCountries = ['US', 'JP', 'KR'];
                        $originCountries = $show->origin_country;
                        
                        foreach ($originCountries as $country) {
                            if (!in_array($country, $allowedCountries)) {
                                return false;
                            }
                        }
                        
                        $hasAllowedCountry = false;
                        foreach ($allowedCountries as $country) {
                            if (in_array($country, $originCountries)) {
                                $hasAllowedCountry = true;
                                break;
                            }
                        }
                        if (!$hasAllowedCountry) {
                            return false;
                        }

                        // Genre filter
                        if (!empty($show->genre_ids)) {
                            $hasExcludedGenre = collect($show->genre_ids)
                                ->intersect($excludedGenres)
                                ->isNotEmpty();
                            if ($hasExcludedGenre) {
                                return false;
                            }
                        }

                        // Keyword filter (hanya untuk US shows)
                        if (in_array('US', $show->origin_country)) {
                            $showName = strtolower($show->name);
                            foreach ($excludedKeywords as $keyword) {
                                if (str_contains($showName, $keyword)) {
                                    return false;
                                }
                            }
                        }

                        // Vote count filter
                        if (!isset($show->vote_count) || $show->vote_count < 1000) {
                            return false;
                        }

                        return $show->vote_average > 5.0;
                    })
                    ->map(function($show) {
                        return (object)[
                            'id' => $show->id,
                            'title' => $show->name,
                            'poster_path' => $show->poster_path,
                            'release_date' => $show->first_air_date,
                            'media_type' => 'tv',
                            'vote_average' => $show->vote_average,
                            'popularity' => $show->popularity,
                            'vote_count' => $show->vote_count ?? 0,
                            'genre_ids' => $show->genre_ids ?? [],
                            'origin_country' => $show->origin_country ?? [],
                            'original_language' => $show->original_language ?? null
                        ];
                    })
                    ->sortByDesc('vote_count')
                    ->values(),
                'total_pages' => $totalPages,
                'current_page' => 1
            ];
        } catch (\Exception $e) {
            \Log::error('TMDB TV Shows Error: ' . $e->getMessage());
            return ['results' => collect([]), 'total_pages' => 0, 'current_page' => 1];
        }
    }

    
} 
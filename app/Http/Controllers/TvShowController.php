<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use App\Helpers\HashidHelper;
use Illuminate\Support\Facades\Cache;

class TvShowController extends Controller
{
    protected $tmdb;
    protected $cache_ttl = 3600; // 1 hour cache

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function index()
    {
       
    }

    public function show($hash)
    {
        try {
            // Decode hash to get real ID
            $id = HashidHelper::decode($hash);
            if (!$id) {
                abort(404);
            }
            
            // Cache TV show details
            $tvShowCacheKey = "tv_show_{$id}_details";
            $tvShow = Cache::remember($tvShowCacheKey, $this->cache_ttl, function() use ($id) {
                return $this->tmdb->getTvShowDetails($id, [
                    'append_to_response' => 'credits,videos,similar'
                ]);
            });

            if (!$tvShow) {
                abort(404);
            }

            // Cache popular shows
            $tmdbService = app(TMDBService::class);
            $popularShowsData = $tmdbService->getPopularTVShows();
            $popularShows = collect($popularShowsData['results'] ?? [])
                ->unique('id')
                ->shuffle()
                ->take(6);

            // Existing seasons caching logic remains unchanged
            $seasons = collect($tvShow->seasons)->filter(function($season) {
                return $season->season_number > 0;
            })->map(function($season) use ($id) {
                $cacheKey = "tv_show_{$id}_season_{$season->season_number}";
                return Cache::remember($cacheKey, $this->cache_ttl, function() use ($id, $season) {
                    $seasonDetails = $this->tmdb->getTvShowSeasons($id, $season->season_number);
                    return (object)[
                        'season_number' => $season->season_number,
                        'name' => $season->name,
                        'episodes' => $seasonDetails->episodes ?? []
                    ];
                });
            });

            // Rest of the code remains the same
            $tvShow->seasons = $seasons;

            $cast = collect($tvShow->credits->cast ?? [])
                ->filter(fn($actor) => !empty($actor->profile_path))
                ->take(8)
                ->values();

            $crew = collect($tvShow->credits->crew ?? [])
                ->filter(fn($person) => !empty($person->profile_path))
                ->take(8)
                ->values();

            // Cache active season
            $activeSeasonNumber = request('season', 1);
            $activeSeasonCacheKey = "tv_show_{$id}_active_season_{$activeSeasonNumber}";
            $activeSeason = Cache::remember($activeSeasonCacheKey, $this->cache_ttl, function() use ($id, $activeSeasonNumber) {
                return $this->tmdb->getTvShowSeasons($id, $activeSeasonNumber);
            });

            return view('seasons', [
                'tvShow' => $tvShow,
                'cast' => $cast,
                'crew' => $crew,
                'videos' => $tvShow->videos->results ?? [],
                'popularShows' => $popularShows,
                'activeSeason' => $activeSeason
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading TV show: ' . $e->getMessage());
            abort(404);
        }
    }

    public function season($hash, $season_number)
    {
        try {
            // Decode hash to get real ID
            $id = HashidHelper::decode($hash);
            if (!$id) {
                abort(404);
            }
            
            // Cache season and show details
            $seasonCacheKey = "tv_show_{$id}_season_{$season_number}_full";
            $data = Cache::remember($seasonCacheKey, $this->cache_ttl, function() use ($id, $season_number) {
                $season = $this->tmdb->getTvShowSeasons($id, $season_number);
                $tvShow = $this->tmdb->getTvShowDetails($id);
                
                if (!$season || !$tvShow) {
                    return null;
                }

                return [
                    'tvShow' => $tvShow,
                    'season' => $season,
                    'episodes' => $season->episodes
                ];
            });

            if (!$data) {
                abort(404);
            }

            return view('episodes', $data);
        } catch (\Exception $e) {
            \Log::error('Error loading season: ' . $e->getMessage());
            abort(404);
        }
    }

    public function episode($hash, $season_number, $episode_number)
    {
        try {
            // Decode hash to get real ID
            $id = HashidHelper::decode($hash);
            if (!$id) {
                abort(404);
            }
            
            // Get TV Show details with append_to_response
            $tvShow = $this->tmdb->getTvShowDetails($id, [
                'append_to_response' => 'credits,similar,videos'
            ]);
            
            if (!$tvShow) {
                abort(404);
            }

            // Get all seasons with episodes
            foreach ($tvShow->seasons as $season) {
                if ($season->season_number === 0) continue;
                $seasonDetails = $this->tmdb->getTvShowSeasons($id, $season->season_number);
                $season->episodes = $seasonDetails->episodes ?? [];
            }

            // Get current season and episode
            $currentSeason = collect($tvShow->seasons)
                ->firstWhere('season_number', $season_number);
            
            if (!$currentSeason) {
                abort(404);
            }

            // Get aired episodes for current season
            $airedEpisodes = collect($currentSeason->episodes)
                ->filter(function($episode) {
                    return isset($episode->air_date) && 
                           \Carbon\Carbon::parse($episode->air_date)->isPast();
                });

            // Get current episode
            $currentEpisode = $airedEpisodes->firstWhere('episode_number', $episode_number);
            
            if (!$currentEpisode) {
                abort(404);
            }

            // Get previous and next episodes
            $prevEpisode = $airedEpisodes
                ->where('episode_number', '<', $episode_number)
                ->last();
                
            $nextEpisode = $airedEpisodes
                ->where('episode_number', '>', $episode_number)
                ->first();

            // If no prev/next episode in current season, check other seasons
            if (!$prevEpisode || !$nextEpisode) {
                $allSeasons = collect($tvShow->seasons)
                    ->filter(fn($s) => $s->season_number > 0)
                    ->sortBy('season_number');

                if (!$prevEpisode) {
                    $prevSeason = $allSeasons
                        ->where('season_number', '<', $season_number)
                        ->last();
                        
                    if ($prevSeason) {
                        $prevSeasonEpisodes = collect($prevSeason->episodes)
                            ->filter(fn($ep) => isset($ep->air_date) && \Carbon\Carbon::parse($ep->air_date)->isPast());
                        $prevEpisode = $prevSeasonEpisodes->last();
                    }
                }

                if (!$nextEpisode) {
                    $nextSeason = $allSeasons
                        ->where('season_number', '>', $season_number)
                        ->first();
                        
                    if ($nextSeason) {
                        $nextSeasonEpisodes = collect($nextSeason->episodes)
                            ->filter(fn($ep) => isset($ep->air_date) && \Carbon\Carbon::parse($ep->air_date)->isPast());
                        $nextEpisode = $nextSeasonEpisodes->first();
                    }
                }
            }

            // Get popular shows using the TMDBService for similar shows
            $tmdbService = app(TMDBService::class);
            $popularShowsData = $tmdbService->getPopularTVShows();
            $popularTVShowsEpisodesPage = collect($popularShowsData['results'] ?? [])
                ->unique('id')
                ->shuffle()
                ->take(6);

            // Debug: Log the similar shows
            \Log::info('Similar Show Episodes:', $popularTVShowsEpisodesPage->toArray());

            return view('episodes', [
                'tvShow' => $tvShow,
                'season' => $currentSeason,
                'episode' => $currentEpisode,
                'prevEpisode' => $prevEpisode,
                'nextEpisode' => $nextEpisode,
                'cast' => array_slice($tvShow->credits->cast ?? [], 0, 8),
                'crew' => array_slice($tvShow->credits->crew ?? [], 0, 8),
                'popularTVShowsEpisodesPage' => $popularTVShowsEpisodesPage
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading episode: ' . $e->getMessage());
            abort(404);
        }
    }


   
}

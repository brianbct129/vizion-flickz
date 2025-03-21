@extends('layouts.app2')

@section('title')
    {{ $tvShow->name }} Season {{ $season->season_number }} Episode {{ $episode->episode_number }}: {{ $episode->name }}
@endsection

@section('description')
    {{ Str::limit($episode->overview, 160) }}
@endsection

@section('keywords')
    {{ $tvShow->name }}, season {{ $season->season_number }}, episode {{ $episode->episode_number }}, {{ $episode->name }}, watch online
@endsection

@section('og_image')
    {{ 'https://image.tmdb.org/t/p/w500' . ($episode->still_path ?? $tvShow->poster_path) }}
@endsection

@section('preloader')
<div id="preloader">
    <div id="loading-center">
        <div id="loading-center-absolute">
            <div class="object" id="object_one"></div>
            <div class="object" id="object_two"></div>
            <div class="object" id="object_three"></div>
        </div>
    </div>
</div>
@endsection

@section('content')
    <!-- breadcrumb-area -->
    <div class="breadcrumb-area market-single-breadcrumb-area">
        <div class="breadcrumb-bg"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="market-single-breadcrumb">
                        <div class="home-back-btn d-none d-lg-block"><a href="{{ route('home') }}">go back to home</a></div>
                        <nav aria-label="breadcrumb" class="d-none d-lg-block">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('tv.show', $tvShow->id) }}">{{ $tvShow->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">S{{ str_pad($season->season_number, 2, '0', STR_PAD_LEFT) }}E{{ str_pad($episode->episode_number, 2, '0', STR_PAD_LEFT) }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- breadcrumb-area-end -->

    <!-- market-single-area -->
    <section class="market-single-area">
        <div class="container">
            <div class="row mb-45">
                <div class="col-12">
                    <div class="activity-table-nav">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="server1-tv" data-bs-toggle="tab" data-bs-target="#server1-tv" type="button"
                                    role="tab" aria-controls="server1" aria-selected="true">Server 1</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="server2-tv" data-bs-toggle="tab" data-bs-target="#server2-tv" type="button"
                                    role="tab" aria-controls="server2" aria-selected="false">Server 2</button>
                            </li>
                        </ul>
                    </div>
                    <div class="standard-blog-item blog-details-content">
                        <div class="blog-thumb">
                            @php
                                // Fungsi untuk mendeteksi apakah season menggunakan continuous episode
                                function isSeasonContinuous($seasons, $currentSeasonNumber) {
                                    // Filter season yang valid (bukan season 0)
                                    $validSeasons = collect($seasons)->filter(fn($s) => $s->season_number > 0);
                                    
                                    // Ambil season saat ini dan season sebelumnya
                                    $currentSeason = $validSeasons->firstWhere('season_number', $currentSeasonNumber);
                                    $previousSeason = $validSeasons->firstWhere('season_number', $currentSeasonNumber - 1);
                                    
                                    // Jika ini season 1 atau tidak ada season sebelumnya
                                    if ($currentSeasonNumber <= 1 || !$previousSeason) return false;
                                    
                                    // Ambil detail episode dari season ini
                                    $seasonEpisodes = isset($currentSeason->episodes) ? collect($currentSeason->episodes) : collect();
                                    $firstEpisodeOfSeason = $seasonEpisodes->first();
                                    
                                    // Jika episode pertama lebih besar dari 1, kemungkinan continuous
                                    return $firstEpisodeOfSeason && $firstEpisodeOfSeason->episode_number > 1;
                                }

                                // Hitung episode number
                                $episodeNumber = $episode->episode_number;
                                $animeSlug = Str::slug($tvShow->name);
                                
                                if(isset($tvShow->origin_country) && in_array('JP', $tvShow->origin_country)) {
                                    // Cek apakah ini continuous episode
                                    $isContinuous = isSeasonContinuous($tvShow->seasons, $season->season_number - 1);
                                    
                                    // Jika bukan continuous dan bukan season 1, hitung ulang episode
                                    if (!$isContinuous && $season->season_number > 1) {
                                        $previousEpisodes = 0;
                                        foreach($tvShow->seasons as $prevSeason) {
                                            if($prevSeason->season_number < $season->season_number && $prevSeason->season_number != 0) {
                                                $previousEpisodes += $prevSeason->episode_count;
                                            }
                                        }
                                        $episodeNumber += $previousEpisodes;
                                    }
                                }

                                $anilistId = null;
                                if (isset($tvShow->origin_country) && in_array('JP', $tvShow->origin_country)) {
                                    $anilistController = new \App\Http\Controllers\AnilistController();
                                    $anilistId = $anilistController->getAnilistId($tvShow->name);
                                }
                            @endphp
                            <script>
                                const tvShowId = {{ $tvShow->id }};
                                const seasonNumber = {{ $season->season_number }};
                                const episodeNumber = {{ $episodeNumber }};
                                const isAnime = {{ isset($tvShow->origin_country) && in_array('JP', $tvShow->origin_country) ? 'true' : 'false' }};
                                const animeSlug = '{{ $animeSlug }}';
                                const anilistId = {{ $anilistId ?? 'null' }};
                            </script>
                            <!-- <iframe id="videoPlayer" 
                                src="@if(isset($tvShow->origin_country) && in_array('JP', $tvShow->origin_country))
                                        @if($anilistId)
                                            https://player.smashy.stream/anime?anilist={{ $anilistId }}&e={{ $episodeNumber }}
                                        @else
                                            https://vidlink.pro/tv/{{ $tvShow->id }}/{{ $season->season_number }}/{{ $episode->episode_number }}?primaryColor=7444EF&secondaryColor=1C1832&iconColor=7444EF&icons=default
                                        @endif
                                    @else
                                        https://vidlink.pro/tv/{{ $tvShow->id }}/{{ $season->season_number }}/{{ $episode->episode_number }}?primaryColor=7444EF&secondaryColor=1C1832&iconColor=7444EF&icons=default
                                    @endif"
                                    frameborder="0" 
                                    loading="lazy" 
                                    decoding="async"
                                    referrerpolicy="origin" 
                                    scrolling="no"
                                    allowfullscreen></iframe> -->
                                    <iframe id="videoPlayer" 
                                src="https://vidsrc.me/embed/tv/{{ $tvShow->id }}/{{ $season->season_number }}/{{ $episode->episode_number }}"
                                    frameborder="0" 
                                    loading="lazy" 
                                    decoding="async"
                                    referrerpolicy="origin" 
                                    scrolling="no"
                                    allowfullscreen></iframe>
                        </div>
                        <div class="standard-blog-content">
                            <h4 class="title">S{{ str_pad($season->season_number, 2, '0', STR_PAD_LEFT) }}E{{ str_pad($episode->episode_number, 2, '0', STR_PAD_LEFT) }}: {{ $episode->name }}</h4>
                            <p>{{ $episode->overview }}</p>
                            <div class="blog-line"></div>
                            <div class="blog-details-bottom">
                                <div class="blog-details-tags">
                                    <ul>
                                        <li class="title">Genres :</li>
                                        @foreach($tvShow->genres as $genre)
                                            <li>
                                                <a href="{{ route('genres.show', [
                                                    'id' => $genre->id, 
                                                    'name' => Str::slug(str_replace(['&', ' '], ['-and-', '-'], $genre->name))
                                                ]) }}">
                                                    {{ $genre->name }}{{ !$loop->last ? ',' : '' }}
                                                </a>
                                        </li>
                                    @endforeach
                                    </ul>
                                </div>
                                <div class="blog-details-social">
                                    <ul>
                                        <li>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::url()) }}" 
                                               target="_blank" 
                                               title="Share on Facebook">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::url()) }}&text={{ urlencode('Check out this episode!') }}" 
                                               target="_blank" 
                                               title="Share on Twitter">
                                                <i class="fab fa-twitter"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://api.whatsapp.com/send?text={{ urlencode('Check out this episode! ' . Request::url()) }}" 
                                               target="_blank" 
                                               title="Share on WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="blog-next-prev mt-50">
                        <ul>
                            <li class="blog-prev">
                                @if($prevEpisode)
                                    <a href="{{ route('tv.episode', [
                                        'id' => $tvShow->id, 
                                        'season' => $prevEpisode->season_number ?? $season->season_number,
                                        'episode' => $prevEpisode->episode_number
                                    ]) }}">
                                        <img src="{{ asset('img/icons/left_arrow.png') }}" alt="Previous">
                                        Previous Episode
                                    </a>
                                @endif
                            </li>
                            <li class="blog-next">
                                @if($nextEpisode)
                                    <a href="{{ route('tv.episode', [
                                        'id' => $tvShow->id, 
                                        'season' => $nextEpisode->season_number ?? $season->season_number,
                                        'episode' => $nextEpisode->episode_number
                                    ]) }}">
                                        Next Episode
                                        <img src="{{ asset('img/icons/right_arrow.png') }}" alt="Next">
                                    </a>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Episode list section -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="general-wrap">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="season-tab" data-bs-toggle="tab" data-bs-target="#season" type="button"
                                    role="tab" aria-controls="season" aria-selected="true">Seasons</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cast-tab" data-bs-toggle="tab" data-bs-target="#cast" type="button"
                                    role="tab" aria-controls="cast" aria-selected="false">Cast</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="season" role="tabpanel" aria-labelledby="season-tab">
                                <div class="season-overflow scroll">
                                    <div class="accordion" id="accordionExample">
                                        @foreach($tvShow->seasons as $seasonItem)
                                            @php
                                                if ($seasonItem->season_number === 0) continue;
                                                $airedEpisodes = collect($seasonItem->episodes ?? [])->filter(function($episode) {
                                                    return isset($episode->air_date) && 
                                                           \Carbon\Carbon::parse($episode->air_date)->isPast();
                                                });
                                                if ($airedEpisodes->isEmpty()) continue;
                                            @endphp
                                            
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <a href="#" 
                                                       class="accordion-button {{ $seasonItem->season_number === $season->season_number ? '' : 'collapsed' }}" 
                                                       data-bs-toggle="collapse" 
                                                       data-bs-target="#collapse{{ $seasonItem->season_number }}" 
                                                       aria-expanded="{{ $seasonItem->season_number === $season->season_number ? 'true' : 'false' }}" 
                                                       aria-controls="collapse{{ $seasonItem->season_number }}">
                                                        Season {{ $seasonItem->season_number }}
                                                    </a>
                                                </h2>
                                                <div id="collapse{{ $seasonItem->season_number }}" 
                                                     class="accordion-collapse collapse {{ $seasonItem->season_number === $season->season_number ? 'show' : '' }}" 
                                                     data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <ul class="season-list">
                                                            @foreach($airedEpisodes as $ep)
                                                            <li>
                                                                <div class="season-item {{ $ep->episode_number === $episode->episode_number && $seasonItem->season_number === $season->season_number ? 'active' : '' }}">
                                                                    <div class="season-avatar">
                                                                        <div class="thumb">
                                                                            <img src="{{ 'https://image.tmdb.org/t/p/w500' . ($ep->still_path ?? '') }}" 
                                                                                 alt="{{ $ep->name }}"
                                                                                 loading="lazy"
                                                                                 onerror="this.src=''">
                                                                        </div>
                                                                        <div class="content">
                                                                            <h5 class="title">
                                                                                <a href="{{ route('tv.episode', ['id' => $tvShow->id, 'season' => $seasonItem->season_number, 'episode' => $ep->episode_number]) }}">
                                                                                    {{ $ep->name }}
                                                                                </a>
                                                                            </h5>
                                                                            <span>S{{ $seasonItem->season_number }}E{{ $ep->episode_number }} / {{ \Carbon\Carbon::parse($ep->air_date)->format('d M Y') }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="cast" role="tabpanel" aria-labelledby="cast-tab">
                                <div class="season-overflow scroll">
                                    <div class="row g-3">
                                        @include('partials.cast-tv')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- market-single-area-end -->

   <!-- top-collection-area -->
    <section class="top-collection-area live-auctions-area">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="section-title mb-40">
                        <h2 class="title">Popular TV Shows <img src="{{ asset('img/icons/title_icon02.png') }}" alt=""></h2>
                    </div>
                </div>
            </div>
            <div class="row gy-3">
                @foreach($popularTVShowsEpisodesPage as $show)
                    <div class="px-2 col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
                        <a href="{{ route('tv.show', $show->id) }}">
                            <div class="movie-card general-card">
                                <div class="content-card">
                                    <img src="{{ 'https://image.tmdb.org/t/p/w500' . $show->poster_path }}" 
                                         alt="{{ $show->title }}"
                                         loading="lazy"
                                         decoding="async"
                                         onerror="this.src=''">
                                    <span class="shadow"></span>
                                    <div class="content">
                                        <h1 class="mb-0">{{ $show->title }}</h1>
                                        <p class="date mb-0">{{ \Carbon\Carbon::parse($show->release_date)->format('Y') }}</p>
                                    </div>
                                </div>
                                <div class="watch-card">
                                    <button>watch now</button>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- top-collection-area-end -->

@endsection


@section('third_script')
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [{
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "{{ url('/') }}"
        },{
            "@type": "ListItem",
            "position": 2,
            "name": "{{ $tvShow->name }}",
            "item": "{{ route('tv.show', $tvShow->id) }}"
        },{
            "@type": "ListItem",
            "position": 3,
            "name": "S{{ str_pad($season->season_number, 2, '0', STR_PAD_LEFT) }}E{{ str_pad($episode->episode_number, 2, '0', STR_PAD_LEFT) }}: {{ $episode->name }}",
            "item": "{{ route('tv.episode', ['id' => $tvShow->id, 'season' => $season->season_number, 'episode' => $episode->episode_number]) }}"
        }]
    }
    </script>
@endsection
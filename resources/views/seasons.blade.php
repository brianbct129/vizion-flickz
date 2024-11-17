@extends('layouts.app2')

@section('title')
    {{ $tvShow->name }} ({{ \Carbon\Carbon::parse($tvShow->first_air_date)->format('Y') }}) - Watch TV Shows Online
@endsection

@section('description')
    {{ Str::limit($tvShow->overview, 160) }}
@endsection

@section('keywords')
    {{ $tvShow->name }}, tv series, watch online, streaming, {{ implode(', ', array_column($tvShow->genres, 'name')) }}
@endsection

@section('og_image')
    {{ 'https://image.tmdb.org/t/p/w500' . $tvShow->poster_path }}
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
                        <div class="home-back-btn"><a href="/">go back to home</a></div>
                        <nav aria-label="breadcrumb" class="d-none d-lg-block">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $tvShow->name }} ({{ \Carbon\Carbon::parse($tvShow->first_air_date)->format('Y') }})</li>
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
            <div class="row">
                <div class="col-lg-3">
                    <div class="d-flex justify-content-center market-single-img">
                        <img src="{{ 'https://image.tmdb.org/t/p/w500' . $tvShow->poster_path }}" alt="{{ $tvShow->name }}" loading="lazy" onerror="this.src=''">
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="market-single-top">
                        <div class="market-single-title-wrap">
                            <h2 class="title text-truncate">{{ $tvShow->name }} ({{ \Carbon\Carbon::parse($tvShow->first_air_date)->format('Y') }})</h2>
                            <ul class="market-details-meta mb-10">
                                <li>{{ \Carbon\Carbon::parse($tvShow->first_air_date)->format('d F Y') }}</li>
                                <li class="wishlist">{{ number_format($tvShow->vote_average, 1) }}</li>
                            </ul>
                        </div>
                        <div class="market-single-action">
                            <ul>
                                <li>
                                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-share-alt"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="market-single-content">
                        <p>{{ $tvShow->overview }}</p>
                    </div>
                  
                    @if(!empty($tvShow->videos->results))
                        @php
                            $trailer = collect($tvShow->videos->results)->firstWhere('type', 'Trailer');
                        @endphp
                        @if($trailer)
                            <a href="https://www.youtube.com/watch?v={{ $trailer->key }}" 
                               class="place-bid-btn" 
                               target="_blank">
                                Watch Trailer
                            </a>
                        @endif
                    @endif
                </div>
                <div class="col-lg-12">
                    <div class="general-wrap">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="season-tab" data-bs-toggle="tab" data-bs-target="#season" type="button"
                                    role="tab" aria-controls="season" aria-selected="true">Episodes</button>
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
                                        @foreach($tvShow->seasons as $season)
                                            @php
                                                // Skip season 0
                                                if ($season->season_number === 0) continue;
                                                // Filter episodes yang sudah tayang
                                                $airedEpisodes = collect($season->episodes ?? [])->filter(function($episode) {
                                                    return isset($episode->air_date) && 
                                                           \Carbon\Carbon::parse($episode->air_date)->isPast();
                                                });
                                                
                                                // Skip season jika belum ada episode yang tayang
                                                if ($airedEpisodes->isEmpty()) continue;
                                            @endphp
                                            
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <a href="#" class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                                       data-bs-toggle="collapse" 
                                                       data-bs-target="#collapse{{ $season->season_number }}" 
                                                       aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                                       aria-controls="collapse{{ $season->season_number }}">
                                                        Season {{ $season->season_number }}
                                                    </a>
                                                </h2>
                                                <div id="collapse{{ $season->season_number }}" 
                                                     class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                                     data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <ul class="season-list">
                                                            @foreach($airedEpisodes as $episode)
                                                            <li>
                                                                <div class="season-item">
                                                                    <div class="season-avatar">
                                                                        <div class="thumb">
                                                                            <img src="{{ 'https://image.tmdb.org/t/p/w500' . ($episode->still_path ?? '') }}" 
                                                                                 alt="{{ $episode->name ?? 'Episode Image' }}">
                                                                        </div>
                                                                        <div class="content">
                                                                            <h5 class="title">
                                                                                <a href="{{ route('tv.episode', ['id' => $tvShow->id, 'season' => $season->season_number, 'episode' => $episode->episode_number]) }}">
                                                                                    {{ $episode->name }}
                                                                                </a>
                                                                            </h5>
                                                                            <span>S{{ $season->season_number }}E{{ $episode->episode_number }} / {{ \Carbon\Carbon::parse($episode->air_date)->format('d M Y') }}</span>
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
    @if(isset($popularShows) && $popularShows->isNotEmpty())
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
                    @foreach($popularShows as $show)
                        <div class="px-2 col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
                            <a href="{{ route('tv.show', $show->id) }}">
                            <div class="movie-card general-card">
                                <div class="content-card">
                                    <img src="{{ 'https://image.tmdb.org/t/p/w500' . $show->poster_path }}" 
                                         alt="{{ $show->title }}"
                                         loading="lazy"
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
    @endif
    <!-- top-collection-area-end -->

@endsection


@section('third_script')
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "TVSeries",
        "name": "{{ $tvShow->name }}",
        "description": "{{ $tvShow->overview }}",
        "image": "{{ 'https://image.tmdb.org/t/p/w500' . $tvShow->poster_path }}",
        "datePublished": "{{ $tvShow->first_air_date }}",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $tvShow->vote_average }}",
            "reviewCount": "{{ $tvShow->vote_count }}"
        }
    }
    </script>
@endsection
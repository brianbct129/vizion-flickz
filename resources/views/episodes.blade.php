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
                        <div class="home-back-btn"><a href="{{ route('home') }}">go back to home</a></div>
                        <nav aria-label="breadcrumb d-none d-lg-block">
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
                    <div class="standard-blog-item blog-details-content">
                        <div class="blog-thumb">
                            <iframe src="https://vidsrc.xyz/embed/tv?tmdb={{ $tvShow->id }}&season={{ $season->season_number }}&episode={{ $episode->episode_number }}&ds_lang=en" 
                                    frameborder="0" 
                                    loading="lazy" 
                                    decoding="async"
                                    referrerpolicy="origin"
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
                                    <div class="row">
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
            <div class="row">
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
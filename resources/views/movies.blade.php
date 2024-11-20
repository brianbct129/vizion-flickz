@extends('layouts.app2')

@section('title')
Watch {{ $movie->title }} ({{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }})
@endsection

@section('description')
    {{ Str::limit($movie->overview, 160) }}
@endsection

@section('keywords')
    {{ $movie->title }}, movie, film, watch online, streaming, {{ $genres->pluck('name')->implode(', ') }}
@endsection

@section('og_image')
    {{ 'https://image.tmdb.org/t/p/w500' . $movie->poster_path }}
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
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ $movie->title }} ({{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }})
                                </li>
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
                <div class="col-12">
                    <div class="standard-blog-item blog-details-content">
                        <div class="blog-thumb">
                            <iframe src="https://vidlink.pro/movie/{{ $movie->id }}?primaryColor=7444EF&secondaryColor=1C1832&iconColor=7444EF&icons=default" 
                                    frameborder="0" 
                                    loading="lazy" 
                                    decoding="async"
                                    referrerpolicy="origin"
                                    allowfullscreen></iframe>
                        </div>
                       
                        <div class="standard-blog-content">
                            <ul class="standard-blog-meta">
                                <li>
                                    <a href="/">
                                        <i class="fa fa-star text-light"></i>{{ number_format($movie->vote_average, 1) }}
                                    </a>
                                </li>
                                <li>
                                    <a href="/">
                                        <i class="flaticon-calendar"></i>{{ \Carbon\Carbon::parse($movie->release_date)->format('d M Y') }}
                                    </a>
                                </li>
                            </ul>
                            <h4 class="title">{{ $movie->title }} ({{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }})</h4>
                            <p>{{ $movie->overview }}</p>
                            <div class="blog-line"></div>
                            <div class="blog-details-bottom">
                                <div class="blog-details-tags">
                                    <ul>
                                        <li class="title">Genres :</li>
                                        @foreach($movie->genres as $genre)
                                        <li>
                                            <a href="{{ route('genres.show', ['id' => $genre->id, 'name' => Str::slug($genre->name)]) }}">
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
                                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::url()) }}&text={{ urlencode($movie->title) }}" 
                                               target="_blank" 
                                               title="Share on Twitter">
                                                <i class="fab fa-twitter"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://api.whatsapp.com/send?text={{ urlencode($movie->title . ' ' . Request::url()) }}" 
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
                </div>
                <div class="col-lg-12">
                    <div class="general-wrap">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="cast-tab" data-bs-toggle="tab" data-bs-target="#cast" type="button"
                                    role="tab" aria-controls="cast" aria-selected="false">Cast</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="cast" role="tabpanel" aria-labelledby="cast-tab">
                                <div class="season-overflow scroll">
                                    <div class="row g-2">
                                        @forelse($cast as $actor)
                                            <div class="col-sm-4">
                                                <div class="cast">
                                                    <div class="thumb">
                                                        <img src="{{ 'https://image.tmdb.org/t/p/w500' . ($actor->profile_path ?? '') }}" 
                                                             alt="{{ $actor->name }}"
                                                             loading="lazy"
                                                             onerror="this.src=''">
                                                    </div>
                                                    <div class="info">
                                                        <h5 class="title">{{ $actor->name }}</h5>
                                                        <span>{{ $actor->character }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            @forelse($crew as $person)
                                                <div class="col-sm-4">
                                                    <div class="cast">
                                                        <div class="thumb">
                                                            @if(!empty($person->profile_path))
                                                                <img src="{{ 'https://image.tmdb.org/t/p/w500' . $person->profile_path }}" 
                                                                     alt="{{ $person->name }}"
                                                                     loading="lazy"
                                                                     onerror="this.src='{{ asset('img/others/heighest_avatar02.png') }}'">
                                                            @else
                                                                <img src="{{ asset('img/others/heighest_avatar02.png') }}" 
                                                                     alt="{{ $person->name }}"
                                                                     loading="lazy">
                                                            @endif
                                                        </div>
                                                        <div class="info">
                                                            <h5 class="title">{{ $person->name }}</h5>
                                                            <span>{{ $person->job }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12 text-center">
                                                    <p>No cast or crew information available.</p>
                                                </div>
                                            @endforelse
                                        @endforelse
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
    @if($popularMovies->isNotEmpty())
    <section class="top-collection-area live-auctions-area">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="section-title mb-40">
                        <h2 class="title">Popular Movies <img src="{{ asset('img/icons/title_icon01.png') }}" alt=""></h2>
                    </div>
                </div>
            </div>
            <div class="row gy-3">
                @foreach($popularMovies as $popular)
                    <div class="px-2 col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
                        <a href="{{ route('movies.show', $popular->id) }}">
                            <div class="movie-card general-card">
                                <div class="content-card">
                                    <img src="{{ 'https://image.tmdb.org/t/p/w500' . $popular->poster_path }}"
                                         alt="{{ $popular->title }}"
                                         loading="lazy">
                                    <span class="shadow"></span>
                                    <div class="content">
                                        <h1 class="mb-0">{{ $popular->title }}</h1>
                                        <p class="date mb-0">{{ \Carbon\Carbon::parse($popular->release_date)->format('Y') }}</p>
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
 <!-- Schema.org Movie markup -->
 <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Movie",
        "name": "{{ $movie->title }}",
        "description": "{{ $movie->overview }}",
        "image": "{{ 'https://image.tmdb.org/t/p/w500' . $movie->poster_path }}",
        "datePublished": "{{ $movie->release_date }}",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $movie->vote_average }}",
            "reviewCount": "{{ $movie->vote_count }}"
        },
        "genre": {!! json_encode($genres->pluck('name')->toArray()) !!},
        "actor": [
            @foreach(collect($cast)->take(5) as $actor)
            {
                "@type": "Person",
                "name": "{{ $actor->name }}"
            }@if(!$loop->last),@endif
            @endforeach
        ]
    }
    </script>
@endsection
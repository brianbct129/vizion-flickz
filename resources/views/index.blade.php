@extends('layouts.app')

@section('title')
{{ config('app.name') }} - Watch Movies, Series, Korean Dramas, Anime with Subtitles Best Quality
@endsection

@section('description')
{{ config('app.name') }} is a free streaming platform for movies, series, anime, and Korean dramas with subtitles. It offers high-quality streaming at no cost, making it ideal for regions without access to cinemas.
@endsection

@section('content')
<div class="banner-bg">
    <!-- banner-area -->
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($bannerMovies as $index => $movie)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" 
                     style="background: url('https://image.tmdb.org/t/p/original{{ $movie->backdrop_path }}') top center/cover no-repeat;">
                    <div class="banner-area">
                        <div class="container">
                            <div class="row justify-content-center justify-content-lg-center justify-content-xl-start">
                                <div class="col-lg-6 col-md-8 col-sm-10">
                                    <div class="banner-content text-center text-lg-center text-xl-start">
                                        <h2 class="title">{{ $movie->title }}</h2>
                                        <p>{{ $movie->overview }}</p>
                                        <a href="{{ route('movies.show', $movie->id) }}" 
                                           class="banner-btn mx-auto mx-lg-0 d-inline-block">
                                            Watch Now <i class="fi-sr-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
            @for($i = 0; $i < min($bannerLimit, count($bannerMovies)); $i++)
                <button type="button" 
                        data-bs-target="#bannerCarousel" 
                        data-bs-slide-to="{{ $i }}" 
                        class="{{ $i === 0 ? 'active' : '' }}">
                </button>
            @endfor
        </div>

      
    </div>
    <!-- banner-area-end -->
</div>

<!-- featured-area -->
<section class="featured-area">
    <div class="container">
        <div class="row mb-25">
            <div class="col-md-6">
                <div class="section-title mb-35">
                    <h2 class="title">Featured <img src="img/icons/title_icon01.png" alt=""></h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="featured-nav"></div>
            </div>
        </div>
        <div class="row top-collection-active">
            @foreach($featured as $movie)
            <div class="px-2 col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
                <a href="{{ route('movies.show', $movie->id) }}">
                    <div class="movie-card featured-card">
                        <div class="content-card">
                            <img src="{{ 'https://image.tmdb.org/t/p/w500' . $movie->poster_path }}">
                            <span class="shadow"></span>
                            <div class="content">
                                <h1 class="mb-0">{{ $movie->title }}</h1>
                                <p class="date mb-0">{{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}</p>
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
<!-- featured-area-end -->

<!-- streaming-features-area -->
<section class="features-app-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8">
                <div class="design-ways-item">
                    <div class="overlay-icon"><i class="fas fa-play-circle"></i></div>
                    <div class="icon"><i class="fas fa-play-circle"></i></div>
                    <div class="content">
                        <h3 class="title">HD Quality</h3>
                        <p>Crystal clear streaming</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8">
                <div class="design-ways-item">
                    <div class="overlay-icon"><i class="fas fa-mobile-alt"></i></div>
                    <div class="icon"><i class="fas fa-mobile-alt"></i></div>
                    <div class="content">
                        <h3 class="title">Multi Device</h3>
                        <p>Watch anywhere</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8">
                <div class="design-ways-item">
                    <div class="overlay-icon"><i class="fas fa-film"></i></div>
                    <div class="icon"><i class="fas fa-film"></i></div>
                    <div class="content">
                        <h3 class="title">Latest Movies</h3>
                        <p>Updated weekly</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8">
                <div class="design-ways-item">
                    <div class="overlay-icon"><i class="fas fa-tv"></i></div>
                    <div class="icon"><i class="fas fa-tv"></i></div>
                    <div class="content">
                        <h3 class="title">TV Shows</h3>
                        <p>Popular series</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- streaming-features-area-end -->

<div class="area-bg">
<!-- week-features-area -->
<section class="week-features-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title mb-35">
                    <h2 class="title">Popular Right Now! <img src="img/icons/title_icon02.png" alt=""></h2>
                </div>
            </div>
        </div>
        <div class="row gy-3">
            @foreach($popular as $item)
            <div class="col-lg-3 col-sm-3 col-6">
                <a href="{{ $item->media_type === 'movie' ? route('movies.show', $item->id) : route('tv.show', $item->id) }}">
                    <div class="movie-card popular-card">
                        <div class="content-card">
                            <img src="{{ 'https://image.tmdb.org/t/p/w500' . $item->poster_path }}">
                            <span class="shadow"></span>
                            <div class="content">
                                <h1 class="mb-0">{{ $item->media_type === 'movie' ? $item->title : $item->name }}</h1>
                                <p class="date mb-0">{{ \Carbon\Carbon::parse($item->media_type === 'movie' ? $item->release_date : $item->first_air_date)->format('Y') }}</p>
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
<!-- week-features-area-end -->

</div>

<!-- explore-products-area -->
<section class="explore-products-area mt-20">
    <div class="container">
        <div class="row gy-4 mb-35">
            <div class="col-md-7 col-sm-8">
                <div class="section-title">
                    <h2 class="title">Explore Anime <img src="img/icons/title_icon01.png" alt=""></h2>
                </div>
            </div>
            <div class="col-md-5 col-sm-4">
                <div class="section-button text-end">
                    <a href="/anime/popular" class="btn filter-btn"> see all</a>
                </div>
            </div>
        </div>
        
        <div class="row gy-3 justify-content-center">
            @foreach($animeList as $anime)
            <div class="px-2 col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
                <a href="{{ $anime->media_type === 'movie' ? route('movies.show', $anime->id) : route('tv.show', $anime->id) }}">
                    <div class="movie-card general-card">
                        <div class="content-card">
                            <img src="{{ 'https://image.tmdb.org/t/p/w500' . $anime->poster_path }}">
                            <span class="shadow"></span>
                            <div class="content">
                                <h1 class="mb-0">{{ $anime->media_type === 'movie' ? $anime->title : $anime->name }}</h1>
                                <p class="date mb-0">{{ \Carbon\Carbon::parse($anime->media_type === 'movie' ? $anime->release_date : $anime->first_air_date)->format('Y') }}</p>
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
<!-- explore-products-area-end -->

<!-- testimonial-area -->
<section class="testimonial--area">
    <div class="container">
        <div class="testimonial-shape-wrap">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-9 col-md-10">
                    <div class="testimonial-active">
                        <div class="testimonial--item text-center">
                            <div class="testimonial-rating">
                                <img src="img/others/star.png" alt="">
                            </div>
                            <div class="testimonial--content">
                                <p>"Oppenheimer is a masterpiece of biographical filmmaking. Christopher Nolan delivers a compelling narrative about the father of the atomic bomb, with stunning cinematography and an outstanding performance by Cillian Murphy."</p>
                                <div class="testimonial--avatar--info">
                                    <h5 class="title">Movie Critics Weekly</h5>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial--item text-center">
                            <div class="testimonial-rating">
                                <img src="img/others/star.png" alt="">
                            </div>
                            <div class="testimonial--content">
                                <p>"Barbie is a colorful, witty, and surprisingly profound exploration of identity and societal expectations. Margot Robbie and Ryan Gosling shine in this imaginative take on the iconic doll's world."</p>
                                <div class="testimonial--avatar--info">
                                    <h5 class="title">Film Review Hub</h5>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial--item text-center">
                            <div class="testimonial-rating">
                                <img src="img/others/star.png" alt="">
                            </div>
                            <div class="testimonial--content">
                                <p>"The latest season of Succession proves why it's the best show on television. Sharp writing, exceptional performances, and a gripping narrative make this HBO series a must-watch for drama enthusiasts."</p>
                                <div class="testimonial--avatar--info">
                                    <h5 class="title">TV Guide Magazine</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- testimonial-area-end -->
@endsection


@section('third_script')
@endsection
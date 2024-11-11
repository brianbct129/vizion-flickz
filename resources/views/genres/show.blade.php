@extends('layouts.app')

@section('title')
{{ config('app.name') }} - Pilihan Genre {{ $currentGenre->name }} Terlengkap
@endsection

@section('description')
{{ $currentGenre->name }} Terlengkap
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
 <section class="breadcrumb-area breadcrumb-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="breadcrumb-content text-center">
                    <h3 class="title">{{ $currentGenre->name }}</h3>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb-area-end -->

@php
    // Array asosiasi genre dengan ikon FontAwesome yang sesuai
    $genreIcons = [
        'Action' => 'fa-fire',
        'Adventure' => 'fa-compass',
        'Animation' => 'fa-child',
        'Comedy' => 'fa-laugh-beam',
        'Crime' => 'fa-gavel',
        'Documentary' => 'fa-video',
        'Drama' => 'fa-theater-masks',
        'Family' => 'fa-users',
        'Fantasy' => 'fa-dragon',
        'History' => 'fa-landmark',
        'Horror' => 'fa-ghost',
        'Music' => 'fa-music',
        'Mystery' => 'fa-eye',
        'Romance' => 'fa-heart',
        'Science Fiction' => 'fa-rocket',
        'TV Movie' => 'fa-tv',
        'Thriller' => 'fa-skull',
        'Western' => 'fa-hat-cowboy'
    ];
@endphp
  <!-- category-area -->
  <div class="category-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <ul class="category-list">
                    @foreach(collect($genres)->take(8) as $genre)
                        <li class="{{ request('genre') == $genre->id ? 'active' : '' }}">
                            <a href="{{ route('genres.show', ['id' => $genre->id, 'name' => strtolower($genre->name)]) }}">
                                <i class="fas {{ $genreIcons[$genre->name] ?? 'fa-film' }} me-2"></i>
                                {{ $genre->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- category-area-end -->

<section class="explore-products-area">
    <div class="container">
        <!-- Movies Section -->
        <div class="row">
            @foreach($content as $item)
                <div class="px-2 col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
                    <div class="movie-item">
                        <a href="{{ isset($item->title) ? route('movies.show', $item->id) : route('tv.show', $item->id) }}">
                            <div class="movie-card general-card">
                                <div class="content-card">
                                    <img src="{{ 'https://image.tmdb.org/t/p/w500' . $item->poster_path }}"
                                         alt="{{ $item->title ?? $item->name }}"
                                         loading="lazy">
                                    <span class="shadow"></span>
                                    <div class="content">
                                        <h1 class="mb-0">{{ $item->title ?? $item->name }}</h1>
                                        <p class="date mb-0">{{ \Carbon\Carbon::parse($item->release_date ?? $item->first_air_date)->format('Y') }}</p>
                                    </div>
                                </div>
                                <div class="watch-card">
                                    <button>watch now</button>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($totalPages > 1)
            <div class="d-flex justify-content-center">
                <div class="pagination-wrap">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            @if($currentPage > 1)
                                <li class="page-item">
                                    <a href="{{ route('genres.show', ['id' => $genreId, 'name' => $genreName, 'page' => $currentPage - 1]) }}" 
                                       class="page-link" aria-label="Previous">
                                       <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                    <a href="{{ route('genres.show', ['id' => $genreId, 'name' => $genreName, 'page' => $i]) }}" 
                                       class="page-link">{{ $i }}</a>
                                </li>
                            @endfor

                            @if($currentPage < $totalPages)
                                <li class="page-item">
                                    <a href="{{ route('genres.show', ['id' => $genreId, 'name' => $genreName, 'page' => $currentPage + 1]) }}" 
                                       class="page-link" aria-label="Next">
                                       <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection 
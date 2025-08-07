@extends('layouts.app')

@php
    use App\Helpers\HashidHelper;
@endphp

@section('title')
{{ config('app.name') }} - {{ $studioName }}
@endsection

@section('description')
Complete list of {{ $studioName }} Movies and TV Shows
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
                    <h3 class="title">{{ $studioName }}</h3>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb-area-end -->

<section class="explore-products-area">
    <div class="container">
        <!-- Content Section -->
        <div class="row gy-3">
            @foreach($content as $item)
                <div class="px-2 col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6">
                    <div class="movie-item">
                        <a href="{{ $item->media_type === 'movie' ? route('movies.show', HashidHelper::encode($item->id)) : route('tv.show', HashidHelper::encode($item->id)) }}">
                            <div class="movie-card general-card">
                                <div class="content-card">
                                    <img src="{{ 'https://image.tmdb.org/t/p/w500' . $item->poster_path }}"
                                         alt="{{ $item->title }}"
                                         loading="lazy">
                                    <span class="shadow"></span>
                                    <div class="content">
                                        <h1 class="mb-0">{{ $item->title }}</h1>
                                        <p class="date mb-0">{{ \Carbon\Carbon::parse($item->release_date)->format('Y') }}</p>
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
                <div class="pagination-wrap mt-30">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            @if($currentPage > 1)
                                <li class="page-item">
                                    <a href="{{ route('studio.marvel', ['page' => $currentPage - 1]) }}" 
                                       class="page-link" aria-label="Previous">
                                       <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                    <a href="{{ route('studio.marvel', ['page' => $i]) }}" 
                                       class="page-link">{{ $i }}</a>
                                </li>
                            @endfor

                            @if($currentPage < $totalPages)
                                <li class="page-item">
                                    <a href="{{ route('studio.marvel', ['page' => $currentPage + 1]) }}" 
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
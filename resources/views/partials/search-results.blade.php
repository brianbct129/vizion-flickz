@forelse($results as $item)
    <div class="search-item p-2">
@php
    $url = match($item->media_type) {
        'movie' => route('movies.show', \App\Helpers\HashidHelper::encode($item->id)),
        'tv', 'anime', 'kdrama', 'jdrama' => route('tv.show', \App\Helpers\HashidHelper::encode($item->id)),
        default => '#'
    };
@endphp
        <a href="{{ $url }}" class="d-flex align-items-center text-dark text-decoration-none">
            <img src="{{ 'https://image.tmdb.org/t/p/w92' . $item->poster_path }}" alt="{{ $item->title }}" class="me-3" style="width: 50px; height: 70px; object-fit: cover;">
            <div>
                <h6 class="mb-0">{{ $item->title }}</h6>
                <small class="text-muted">
                    @if(isset($item->release_date))
                        {{ \Carbon\Carbon::parse($item->release_date)->year }} •
                    @endif
                    {{ match($item->media_type) {
                        'movie' => 'Movie',
                        'tv' => 'TV Show',
                        'anime' => 'Anime',
                        'kdrama' => 'K-Drama',
                        'jdrama' => 'J-Drama',
                        default => 'Movie'
                    } }}
                </small>
            </div>
        </a>
    </div>
@empty
    <div class="search-item p-2 text-center text-light">No results found.</div>
@endforelse

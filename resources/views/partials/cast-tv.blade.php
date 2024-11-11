@forelse($cast as $actor)
    @if(!empty($actor->profile_path))
        <div class="col-sm-4">
            <div class="cast">
                <div class="thumb">
                    <img src="{{ 'https://image.tmdb.org/t/p/w500' . $actor->profile_path }}" 
                         alt="{{ $actor->name }}"
                         loading="lazy">
                </div>
                <div class="info">
                    <h5 class="title">{{ $actor->name }}</h5>
                    <span>{{ $actor->character }}</span>
                </div>
            </div>
        </div>
    @endif
@empty
    @forelse($crew as $person)
        @if(!empty($person->profile_path))
            <div class="col-sm-4">
                <div class="cast">
                    <div class="thumb">
                        <img src="{{ 'https://image.tmdb.org/t/p/w500' . $person->profile_path }}" 
                             alt="{{ $person->name }}"
                             loading="lazy">
                    </div>
                    <div class="info">
                        <h5 class="title">{{ $person->name }}</h5>
                        <span>{{ $person->job }}</span>
                    </div>
                </div>
            </div>
        @endif
    @empty
        <div class="col-12 text-center">
            <p>No crew information available.</p>
        </div>
    @endforelse
@endforelse
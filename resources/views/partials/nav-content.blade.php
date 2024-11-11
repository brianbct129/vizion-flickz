<nav class="menu-nav">
    <div class="logo"><a href="/"><img src="{{ asset('img/logo/naftmak.svg') }}" alt=""></a></div>
    <div class="header-form position-relative">
        <form id="searchForm" action="{{ route('search') }}" method="GET">
            <button type="submit" disabled><i class="flaticon-search"></i></button>
            <input type="text" 
                   name="q" 
                   id="searchInput"
                   placeholder="Search..."
                   autocomplete="off"
                   required>
        </form>
        <div id="searchResults" class="search-results position-absolute w-100 rounded mt-1" style="max-height: 300px; overflow-y: auto; display: none;">
            <!-- Loading indicator di dalam searchResults -->
            <div id="searchLoading" class="text-center py-4" style="display: none;">
                <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <!-- Search results will be appended here -->
            <div id="searchResultsContent">
            </div>
        </div>
    </div>
    <div class="navbar-wrap main-menu d-none d-lg-flex">
        <ul class="navigation">
            <li class="{{ request()->is('/') ? 'active' : '' }}"><a href="/">Home</a></li>
            <li class="menu-item-has-children {{ request()->is('studio/*') || request()->is('popular/movies') ? 'active' : '' }}">
                <a href="#">Movies</a>
                <ul class="submenu">
                    <li class="{{ request()->is('studio/marvel') ? 'active' : '' }}">
                        <a href="{{ route('studio.marvel') }}" 
                           class="{{ request()->is('studio/marvel') ? 'active' : '' }}">Marvel Universe</a>
                    </li>
                    <li class="{{ request()->is('studio/dc') ? 'active' : '' }}">
                        <a href="{{ route('studio.dc') }}" 
                           class="{{ request()->is('studio/dc') ? 'active' : '' }}">DC Universe</a>
                    </li>
                    <li class="{{ request()->is('popular/movies') ? 'active' : '' }}">
                        <a href="{{ route('popular.movies') }}" 
                           class="{{ request()->is('popular/movies') ? 'active' : '' }}">Popular Movies</a>
                    </li>
                </ul>
            </li>
            <li class="menu-item-has-children {{ request()->is('popular/shows') || request()->is('kdrama') ? 'active' : '' }}">
                <a href="#">TV Shows</a>
                <ul class="submenu">
                    <li class="{{ request()->is('popular/shows') ? 'active' : '' }}">
                        <a href="{{ route('popular.tvshows') }}" 
                           class="{{ request()->is('popular/shows') ? 'active' : '' }}">Popular Shows</a>
                    </li>
                    <li class="{{ request()->is('kdrama') ? 'active' : '' }}">
                        <a href="{{ route('drakor.index') }}" 
                           class="{{ request()->is('kdrama') ? 'active' : '' }}">Korean Drama</a>
                    </li>
                </ul>
            </li>
            <li class="menu-item-has-children {{ request()->is('anime/*') ? 'active' : '' }}">
                <a href="#">Anime</a>
                <ul class="submenu">
                    <li class="{{ request()->is('anime/popular') ? 'active' : '' }}">
                        <a href="{{ route('anime.popular') }}">Popular Anime</a>
                    </li>
                    <li class="{{ request()->is('anime/movies') ? 'active' : '' }}">
                        <a href="{{ route('anime.movies') }}">Anime Movies</a>
                    </li>
                </ul>
            </li>
            <li class="menu-item-has-children {{ request()->is('genre/*') ? 'active' : '' }}">
                <a href="#">Genres</a>
                <ul class="submenu">
                    @foreach($genres as $genre)
                        <li class="{{ request()->is('genre/' . $genre->id . '/' . strtolower($genre->name)) ? 'active' : '' }}">
                            <a href="{{ route('genres.show', ['id' => $genre->id, 'name' => strtolower($genre->name)]) }}">
                                {{ $genre->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>
    </div>
    <div class="header-action d-none d-xl-block">
        <ul>
            <li class="header-btn"><a href="https://discord.gg/RbBHxF4Z" target="_blank" class="btn"><i class="fab fa-discord me-1"></i> Discord</a></li>
        </ul>
    </div>
</nav>
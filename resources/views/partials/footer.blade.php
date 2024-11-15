<footer>
    <div class="footer-top-wrap">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-xl-3 col-lg-4 col-md-5 col-sm-9">
                    <div class="footer-widget">
                        <div class="footer-logo mb-25">
                            <a href="/"><img src="{{ asset('img/logo/logo.png') }}" alt=""></a>
                        </div>
                        <p>{{ config('app.name') }} is a free streaming platform for movies, series, anime, and Korean dramas with Indonesian subtitles. It offers high-quality streaming at no cost, making it ideal for regions without access to cinemas.</p>
                        <ul class="footer-social">
                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                            <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="footer-widget">
                        <h4 class="fw-title">Genres</h4>
                        <ul class="fw-links">
                            @forelse($footerGenres as $genre)
                                <li>
                                    <a href="{{ route('genres.show', ['id' => $genre->id, 'name' => \Str::slug($genre->name)]) }}">
                                        {{ $genre->name }}
                                    </a>
                                </li>
                            @empty
                                <li>Action</li>
                                <li>Adventure</li>
                                <li>Comedy</li>
                                <li>Drama</li>
                                <li>Sci-Fi</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <div class="footer-widget">
                        <h4 class="fw-title">Series</h4>
                        <ul class="fw-links">
                            <li><a href="/studio/marvel">Marvel Universe</a></li>
                            <li><a href="/studio/dc">DC Universe</a></li>
                            <li><a href="/anime/popular">Anime</a></li>
                            <li><a href="/kdrama">Korean Drama</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <h4 class="fw-title">Subscribe Us</h4>
                        <form action="#" class="newsletter-form">
                            <input type="email" placeholder="info@youmail.com">
                            <button type="submit"><i class="flaticon-small-rocket-ship-silhouette"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-wrap">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="copyright-text">
                        <p>All rights reserved © {{ date('Y') }} by <a href="/">{{ config('app.name') }}</a></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <ul class="copyright-link-list">
                        <li><a href="/">Privacy Policy</a></li>
                        <li><a href="/">Terms And Condition</a></li>
                        <li><a href="/">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
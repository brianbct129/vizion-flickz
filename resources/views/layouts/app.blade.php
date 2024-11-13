<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>@yield('title') - {{ config('app.name') }}</title>
        <meta name="description" content="@yield('description')">
        <meta name="keywords" content="@yield('keywords', 'movies, tv shows, watch online, streaming')">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="referrer" content="origin">

		<link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/favicon.png') }}">
        <!-- Place favicon.ico in the root directory -->

        <meta property="og:title" content="@yield('title')">
        <meta property="og:description" content="@yield('description')">
        <meta property="og:image" content="@yield('og_image', asset('img/logo.png'))">
        <meta property="og:url" content="{{ request()->url() }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@yield('title')">
        <meta name="twitter:description" content="@yield('description')">
        <meta name="twitter:image" content="@yield('og_image', asset('img/logo.png'))">

        <link rel="canonical" href="{{ request()->url() }}">
        <!-- Place favicon.ico in the root directory -->

		<!-- CSS here -->
        @include('partials.style')
    </head>
    <body>

        <!-- preloader -->
        <div id="preloader">
            <div id="loading-center">
                <div id="loading-center-absolute">
                    <div class="object" id="object_one"></div>
                    <div class="object" id="object_two"></div>
                    <div class="object" id="object_three"></div>
                </div>
            </div>
        </div>
        <!-- preloader-end -->

		<!-- Scroll-top -->
        <button class="scroll-top scroll-to-target" data-target="html">
            <i class="fas fa-angle-up"></i>
        </button>
        <!-- Scroll-top-end-->

        <!-- main-content -->
        <div class="main-content">
            <!-- header-area -->
            @include('partials.header')
            <!-- header-area-end -->

            <!-- main-area -->
            <main>
               @yield('content')
            </main>
            <!-- main-area-end -->


            <!-- footer-area -->
            @include('partials.footer')
            <!-- footer-area-end -->

        </div>
        <!-- main-content-end -->



		<!-- JS here -->
        @include('partials.script')
        @yield('third_script')
    </body>
</html>
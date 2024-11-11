<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>@yield('title') - {{ config('app.name') }}</title>
        <meta name="description" content="@yield('description')">
        <meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/favicon.png') }}">
        <!-- Place favicon.ico in the root directory -->

		<!-- CSS here -->
        @include('partials.style')
    </head>
    <body>

        <!-- preloader -->
        @yield('preloader')
        <!-- preloader-end -->

		<!-- Scroll-top -->
        <button class="scroll-top scroll-to-target" data-target="html">
            <i class="fas fa-angle-up"></i>
        </button>
        <!-- Scroll-top-end-->

        <!-- main-content -->
        <div class="main-content">
            <!-- header-area -->
            @include('partials.single-header')
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
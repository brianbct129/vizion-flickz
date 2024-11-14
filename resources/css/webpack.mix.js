const mix = require('laravel-mix');

// CSS Files - hanya mix yang tidak ada .min
mix.styles('resources/css/magnific-popup.css', 'public/css/min.magnific-popup.css')
   .styles('resources/css/uicons-solid-rounded.css', 'public/css/min.uicons-solid-rounded.css')
   .styles('resources/css/flaticon.css', 'public/css/min.flaticon.css')
   .styles('resources/css/slick.css', 'public/css/min.slick.css')
   .styles('resources/css/default.css', 'public/css/min.default.css')
   .styles('resources/css/style.css', 'public/css/min.style.css')
   .styles('resources/css/responsive.css', 'public/css/min.responsive.css');

// JavaScript Files - hanya mix yang tidak ada .min
mix.scripts('resources/js/plugins.js', 'public/js/min.plugins.js')
   .scripts('resources/js/main.js', 'public/js/min.main.js');

mix.version();
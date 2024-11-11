<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\ViewComposers\GenreComposer;
use App\Http\ViewComposers\FooterComposer;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share genres dengan semua view
        View::composer('*', GenreComposer::class);

        // Daftarkan composer untuk footer
        View::composer('partials.footer', FooterComposer::class);
    }
}

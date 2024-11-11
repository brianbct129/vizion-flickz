<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_plugin')
])->group(function () {
    Route::get('/movies/popular', [MovieController::class, 'getPopularMovies']);
    Route::get('/movies/search/{query}', [MovieController::class, 'searchMovies']);
}); 
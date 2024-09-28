<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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

Route::get('/', [VideoController::class, 'index']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/videos', [VideoController::class, 'index']);

Route::middleware(['web'])->prefix('api')->group(function () {
    Route::get('/', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        return 'hello';
    });
    Route::post('/videos/{video}/dislike', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        return 'hello';
    });
    Route::post('/videos/{video}/like', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        return 'hello';
    });
    Route::post('/videos/{video}/undislike', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        return 'hello';
    });
    Route::post('/videos/{video}/unlike', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        return 'hello';
    });
    Route::post('/videos/{video}/viewed', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        return $request->input();
    });
    Route::post('/videos/{video}/comment', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        return [
            'sess' => \Illuminate\Support\Facades\Session::getId(),
            'de' => 'dede',
        ];
    });
    Route::get('/videos/{video}/counts', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        return [
            'likes_count' => 1,
            'dislikes_count' => 2,
        ];
    });
});

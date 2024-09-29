<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Video;
use App\Jobs\RequestJob;
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
    Route::get('/api/videos', function (Request $request) {
//        RateLimiter::clear('login.'.$request->ip());
        $videos = Video::inRandomOrder()->get();
        return response()->json($videos);
    });

    Route::get('/', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        return 'hello';
    });
    Route::get('/', [VideoController::class, 'index']);
    Route::post('/videos/{video}/dislike', [VideoController::class, 'action']);
    Route::post('/videos/{video}/like', [VideoController::class, 'action']);
    Route::post('/videos/{video}/undislike', [VideoController::class, 'action']);
    Route::post('/videos/{video}/unlike', [VideoController::class, 'action']);
    Route::post('/videos/{video}/viewed', [VideoController::class, 'action']);
    Route::post('/videos/{video}/comment', [VideoController::class, 'action']);
});

<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Video;

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
    Route::post('/videos/{video}/dislike', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        $url = env("MAIN_API_URL", "http://5.35.94.149:5000/receive");

        // Тело запроса, например, массив данных
        $requestBody = [
            'session_id' => \Illuminate\Support\Facades\Session::getId(),
            'video_id' => ['00000859-f2bf-4578-8d90-429d4c0e5c9c'],
            'action' => 'dislike',
            'info' => [
                'foo' => 80
            ]
        ];
        $response = Http::post($url, $requestBody);
        $responseBody = $response->body(); // Вся причина
        return $responseBody;
    });
    Route::post('/videos/{video}/like', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        $url = env("MAIN_API_URL", "http://5.35.94.149:5000/receive");

        // Тело запроса, например, массив данных
        $requestBody = [
            'session_id' => \Illuminate\Support\Facades\Session::getId(),
            'video_id' => ['00000859-f2bf-4578-8d90-429d4c0e5c9c'],
            'action' => 'like',
            'info' => [
                'foo' => 80
            ]
        ];
        $response = Http::post($url, $requestBody);
        $responseBody = $response->body(); // Вся причина
        return $responseBody;
    });
    Route::post('/videos/{video}/undislike', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        $url = env("MAIN_API_URL", "http://5.35.94.149:5000/receive");

        // Тело запроса, например, массив данных
        $requestBody = [
            'session_id' => \Illuminate\Support\Facades\Session::getId(),
            'video_id' => ['00000859-f2bf-4578-8d90-429d4c0e5c9c'],
            'action' => 'undislike',
            'info' => [
                'foo' => 80
            ]
        ];
        $response = Http::post($url, $requestBody);
        $responseBody = $response->body(); // Вся причина
        return $responseBody;
    });
    Route::post('/videos/{video}/unlike', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        $url = env("MAIN_API_URL", "http://5.35.94.149:5000/receive");

        // Тело запроса, например, массив данных
        $requestBody = [
            'session_id' => \Illuminate\Support\Facades\Session::getId(),
            'video_id' => ['00000859-f2bf-4578-8d90-429d4c0e5c9c'],
            'action' => 'unlike',
            'info' => [
                'foo' => 80
            ]
        ];
        $response = Http::post($url, $requestBody);
        $responseBody = $response->body(); // Вся причина
        return $responseBody;
    });
    Route::post('/videos/{video}/viewed', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        $url = env("MAIN_API_URL", "http://5.35.94.149:5000/receive");

        // Тело запроса, например, массив данных
        $requestBody = [
            'session_id' => \Illuminate\Support\Facades\Session::getId(),
            'video_id' => ['00000859-f2bf-4578-8d90-429d4c0e5c9c'],
            'action' => 'stop',
            'info' => [
                'percentage' => $request->input('percentage')
            ]
        ];
        $response = Http::post($url, $requestBody);
        $responseBody = $response->body(); // Вся причина
        return $responseBody;
    });
    Route::post('/videos/{video}/comment', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        $url = env("MAIN_API_URL", "http://5.35.94.149:5000/receive");

        // Тело запроса, например, массив данных
        $requestBody = [
            'session_id' => \Illuminate\Support\Facades\Session::getId(),
            'video_id' => ['00000859-f2bf-4578-8d90-429d4c0e5c9c'],
            'action' => 'comment',
            'info' => [
                'foo' => 80
            ]
        ];
        $response = Http::post($url, $requestBody);
        $responseBody = $response->body(); // Вся причина
        return $responseBody;
    });
    Route::get('/videos/{video}/counts', function (Request $request) {
        RateLimiter::clear('login.'.$request->ip());
        return [
            'likes_count' => 1,
            'dislikes_count' => 2,
        ];
    });
});

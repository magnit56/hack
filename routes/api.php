<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

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
    return \Illuminate\Support\Facades\Session::getId();
});
Route::get('/videos/{video}/counts', function (Request $request) {
    RateLimiter::clear('login.'.$request->ip());
    return [
        'likes_count' => 1,
        'dislikes_count' => 2,
    ];
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return 'hello';
});
Route::post('/videos/{video}/dislike', function () {
    return 'hello';
});
Route::post('/videos/{video}/like', function () {
    return 'hello';
});
Route::post('/videos/{video}/undislike', function () {
    return 'hello';
});
Route::post('/videos/{video}/unlike', function () {
    return 'hello';
});
Route::post('/videos/{video}/viewed', function (Request $request) {
    return $request->input();
});
Route::get('/videos/{video}/counts', function () {
    return [
        'likes_count' => 1,
        'dislikes_count' => 2,
    ];
});

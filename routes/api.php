<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LinkShortenerController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::get('/refresh', 'refresh');
});


Route::controller(LinkShortenerController::class)->group(function () {
    Route::get('/links/all', 'index');
    Route::get('/links/{id}', 'show');
    Route::get('/{shortLink}', 'redirect');
    Route::post('/links', 'store');
    Route::put('/links/{id}', 'update');
    Route::delete('/links/{id}', 'delete');
});

Route::controller(UserController::class)->group(function () {
    Route::get('/user/all', 'index');
    Route::get('/user/{id}', 'show');
    Route::post('/user', 'store');
    Route::put('/user/{id}', 'update');
    Route::delete('/user/{id}', 'delete');
});

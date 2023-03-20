<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::apiResource('/kehadirans', App\Http\Controllers\Api\KehadiranController::class);
Route::apiResource('/posts', App\Http\Controllers\Api\PostController::class);
Route::apiResource('/siswas', App\Http\Controllers\Api\SiswaController::class);
Route::apiResource('/feedback', App\Http\Controllers\Api\FeedbackController::class);
Route::apiResource('/izin', App\Http\Controllers\Api\IzinController::class);

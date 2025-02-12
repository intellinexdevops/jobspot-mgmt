<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('signin', [AuthController::class, 'signin']);
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('check', [AuthController::class, 'check']);
});

Route::middleware(['auth:api'])->group(function ($router) {
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('profile', [AuthController::class, 'profile']);
    Route::post('profileSetup', [AuthController::class, 'profileSetup']);
});

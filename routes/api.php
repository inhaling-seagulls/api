<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileProjectController;
use App\Http\Controllers\TagController;
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

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::apiResource('tags', TagController::class, ['only' => ['index']]);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('me', [AuthController::class, 'me']);
        Route::get('logout', [AuthController::class, 'logout']);

        Route::apiResource('profiles', ProfileController::class);

        Route::apiResource('projects', ProjectController::class, ['only' => ['index', 'show']]);
        Route::apiResource('profiles/{profile}/projects', ProfileProjectController::class);
    });
});

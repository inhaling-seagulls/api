<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileProjectController;
use App\Http\Controllers\TagController;
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

Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::apiResource('profiles', ProfileController::class, ['only' => ['index', 'show']])->middleware('auth:sanctum');
    Route::apiResource('profiles/{profile}/projects', ProfileProjectController::class)->middleware('auth:sanctum');

    Route::apiResource('projects', ProjectController::class)->middleware('auth:sanctum');
    Route::apiResource('tags', TagController::class, ['only' => ['index']]);
});

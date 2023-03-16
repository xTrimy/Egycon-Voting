<?php

use App\Http\Controllers\API\CosplayerController;
use App\Http\Controllers\API\EventController;
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

Route::apiResource('cosplayers', CosplayerController::class);
Route::get('cosplayer/search_num/{number}', [CosplayerController::class, 'search_cosplayer_by_number'])->name('cosplayers.search_by_number');
Route::apiResource('events', EventController::class);

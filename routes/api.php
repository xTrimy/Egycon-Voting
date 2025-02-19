<?php

use App\Http\Controllers\API\CosplayerController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\TelegramController;
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
Route::get('cosplayer/search_num/{event_id}/{number}', [CosplayerController::class, 'search_cosplayer_by_number_with_event_id']);
Route::get('cosplayers/event/{event_id}', [CosplayerController::class, 'get_all_by_event_id']);
Route::apiResource('events', EventController::class);


Route::any('/telegram', [TelegramController::class, 'index']);

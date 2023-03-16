<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CosplayerController;
use App\Http\Controllers\CosplayerVoteController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PollDataController;
use App\Http\Controllers\PollVoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('cosplayers.index');
})->name('home');

Route::get('/poll/{id}', [PollVoteController::class, 'index'])->name('voting_page.index');
Route::post('/poll/{id}', [PollVoteController::class, 'store'])->name('voting_page.store');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::prefix('/admin')->middleware('auth')->group(function(){

    // Route::resource('cosplayers', CosplayerController::class);
    // split cosplayers resource into separate routes to allow for nested resources
    Route::get('cosplayers', [CosplayerController::class, 'index'])->name('cosplayers.index');
    Route::middleware('check_permissions:admin')->group(function () {
        Route::get('cosplayers/create', [CosplayerController::class, 'create'])->name('cosplayers.create');
        Route::post('cosplayers', [CosplayerController::class, 'store'])->name('cosplayers.store');
        Route::get('cosplayers/{cosplayer}', [CosplayerController::class, 'show'])->name('cosplayers.show');
        Route::get('cosplayers/{cosplayer}/edit', [CosplayerController::class, 'edit'])->name('cosplayers.edit');
        Route::put('cosplayers/{cosplayer}', [CosplayerController::class, 'update'])->name('cosplayers.update');
        Route::delete('cosplayers/{cosplayer}', [CosplayerController::class, 'destroy'])->name('cosplayers.destroy');
        Route::resource('events', EventController::class);
        Route::resource('polls', PollController::class);
        Route::resource('polls/{poll}/poll_data', PollDataController::class);
        Route::get('polls/{poll}/votes', [PollController::class, 'votes'])->name('polls.votes');
        Route::get('top', [CosplayerController::class, 'top_cosplayers']);
        Route::get('generate_cosplayers_poll', [CosplayerController::class, 'create_poll_from_top_cosplayers']);

    });
    
    // images and references are nested resources of cosplayers
    Route::get('cosplayer/search_num', [CosplayerController::class, 'search_cosplayer_by_number'])->name('cosplayers.search_by_number');
    Route::prefix('cosplayers')->group(function () {
        Route::prefix('/c/{cosplayer}')->group(function () {
            Route::prefix('images')->middleware('check_permissions:admin')->group(function () {
                Route::get('/', [CosplayerController::class, 'addImagesView'])->name('cosplayers.images');
                Route::post('/', [CosplayerController::class, 'addImages'])->name('cosplayers.images.store');
                Route::delete('/{image}', [CosplayerController::class, 'removeImage'])->name('cosplayers.images.destroy');
            });
            Route::prefix('references')->middleware('check_permissions:admin')->group(function () {
                Route::get('/', [CosplayerController::class, 'addReferencesView'])->name('cosplayers.references');
                Route::post('/', [CosplayerController::class, 'addReferences'])->name('cosplayers.references.store');
                Route::delete('/{reference}', [CosplayerController::class, 'removeReference'])->name('cosplayers.references.destroy');
            });
            Route::prefix('vote')->group(function () {
                Route::get('/', [CosplayerVoteController::class, 'create'])->name('cosplayers.vote.create');
                Route::post('/', [CosplayerVoteController::class, 'store'])->name('cosplayers.vote.store');
            });
        });
    });

});

// Route::get('/add-references', function () {
//     $cosplayers = \App\Models\Cosplayer::all();
//     foreach ($cosplayers as $cosplayer) {
//         $reference = new \App\Models\CosplayerReference();
//         $reference->cosplayer_id = $cosplayer->id;
//         $reference->image = "references/" . $cosplayer->number . ".jpg";
//         $reference->save();
//     }
// })->name('add_references');
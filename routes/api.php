<?php

use App\Http\Controllers\API\V1\PlaylistController;
use App\Http\Controllers\API\V1\SpotifyController;
use App\Http\Middleware\SpotifyTokenRefresh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'v1'], function() {
    Route::group(['prefix' => 'playlist'], function() {
        Route::post('create', [PlaylistController::class, 'create'])->middleware(SpotifyTokenRefresh::class);
        Route::group(['prefix' => '{playlist_id}'], function() {
            Route::post('add-tracks', [PlaylistController::class, 'addTracks']);
        });
    });

    Route::get('/callback', [SpotifyController::class, 'callback']);
});

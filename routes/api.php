
<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\TextPassageController;
use App\Http\Controllers\UserStatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/passages', [TextPassageController::class, 'index']);
Route::get('/passages/{textPassage}', [TextPassageController::class, 'show']);

Route::get('/leaderboard', [LeaderboardController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/game/submit', [GameController::class, 'store']);
    Route::get('/user/history', [UserStatsController::class, 'history']);
});

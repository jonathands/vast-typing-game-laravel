
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameDataController;
use App\Http\Controllers\Api\GamePlayController;
use App\Http\Controllers\Api\RankingController;
use App\Http\Controllers\Api\UserStatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('game-data')->group(function () {
    Route::get('/', [GameDataController::class, 'index']);
    Route::get('/random', [GameDataController::class, 'random']);
    Route::get('/{id}', [GameDataController::class, 'show']);
});

Route::middleware(['auth:sanctum'])->prefix('game-play')->group(function () {
    Route::post('/start', [GamePlayController::class, 'start']);
    Route::post('/submit-word', [GamePlayController::class, 'submitWord']);
    Route::post('/finish', [GamePlayController::class, 'finish']);
    Route::post('/calculate-stats', [GamePlayController::class, 'calculateStats']);
});

Route::middleware(['auth:sanctum'])->get('/user/history', [UserStatsController::class, 'history']);

Route::prefix('rankings')->group(function () {
    Route::get('/general', [RankingController::class, 'general']);
    Route::get('/accuracy', [RankingController::class, 'accuracy']);
    Route::get('/speed', [RankingController::class, 'speed']);
});

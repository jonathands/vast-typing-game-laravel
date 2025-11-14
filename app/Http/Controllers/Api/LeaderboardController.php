<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardResource;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->query('limit', 10);

        $leaderboard = User::query()
            ->select('users.*')
            ->selectRaw('MAX(results.wpm) as best_wpm')
            ->selectRaw('MAX(results.accuracy) as best_accuracy')
            ->selectRaw('COUNT(results.id) as total_games')
            ->join('results', 'users.id', '=', 'results.user_id')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.email_verified_at', 'users.password', 'users.remember_token', 'users.created_at', 'users.updated_at', 'users.two_factor_secret', 'users.two_factor_recovery_codes', 'users.two_factor_confirmed_at')
            ->orderByDesc('best_wpm')
            ->limit($limit)
            ->get();

        $leaderboard->each(function ($user, $index) {
            $user->rank = $index + 1;
        });

        return LeaderboardResource::collection($leaderboard);
    }
}

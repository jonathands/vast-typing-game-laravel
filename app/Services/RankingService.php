<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RankingService
{
    protected function baseRankingQuery(): Builder
    {
        return User::query()
            ->select('users.*')
            ->selectRaw('AVG(results.wpm) as avg_wpm')
            ->selectRaw('AVG(results.accuracy) as avg_accuracy')
            ->selectRaw('COUNT(results.id) as total_games')
            ->join('results', 'users.id', '=', 'results.user_id')
            ->groupBy(
                'users.id',
                'users.name',
                'users.email',
                'users.email_verified_at',
                'users.password',
                'users.remember_token',
                'users.created_at',
                'users.updated_at',
                'users.two_factor_secret',
                'users.two_factor_recovery_codes',
                'users.two_factor_confirmed_at'
            );
    }

    protected function assignRanks(Collection $collection): Collection
    {
        $collection->each(function ($user, $index) {
            $user->rank = $index + 1;
        });

        return $collection;
    }

    public function getGeneralRanking(int $limit = 10): Collection
    {
        $ranking = $this->baseRankingQuery()
            ->selectRaw('(AVG(results.wpm) + AVG(results.accuracy)) / 2 as score')
            ->orderByDesc('score')
            ->limit($limit)
            ->get();

        return $this->assignRanks($ranking);
    }

    public function getAccuracyRanking(int $limit = 10): Collection
    {
        $ranking = $this->baseRankingQuery()
            ->selectRaw('AVG(results.accuracy) as score')
            ->orderByDesc('score')
            ->limit($limit)
            ->get();

        return $this->assignRanks($ranking);
    }

    public function getSpeedRanking(int $limit = 10): Collection
    {
        $ranking = $this->baseRankingQuery()
            ->selectRaw('AVG(results.wpm) as score')
            ->where('results.accuracy', '>=', 85)
            ->orderByDesc('score')
            ->limit($limit)
            ->get();

        return $this->assignRanks($ranking);
    }

    public function getTopRankings(int $limit = 10): Collection
    {
        $leaderboard = User::query()
            ->select('users.*')
            ->selectRaw('MAX(results.wpm) as best_wpm')
            ->selectRaw('MAX(results.accuracy) as best_accuracy')
            ->selectRaw('COUNT(results.id) as total_games')
            ->selectRaw('AVG(results.wpm) as avg_wpm')
            ->selectRaw('AVG(results.accuracy) as avg_accuracy')
            ->join('results', 'users.id', '=', 'results.user_id')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at', 'users.updated_at')
            ->orderByDesc('best_wpm')
            ->limit($limit)
            ->get();

        return $this->assignRanks($leaderboard);
    }

    public function getRankingForPassage(int $textPassageId, int $limit = 10): Collection
    {
        $leaderboard = User::query()
            ->select('users.*')
            ->selectRaw('MAX(results.wpm) as best_wpm')
            ->selectRaw('MAX(results.accuracy) as best_accuracy')
            ->selectRaw('COUNT(results.id) as total_attempts')
            ->join('results', 'users.id', '=', 'results.user_id')
            ->where('results.text_passage_id', $textPassageId)
            ->groupBy('users.id', 'users.name', 'users.email',  'users.created_at', 'users.updated_at')
            ->orderByDesc('best_wpm')
            ->limit($limit)
            ->get();

        return $this->assignRanks($leaderboard);
    }

    public function formatRanking(Collection $rankings): array
    {
        return $rankings->map(function ($user) {
            return [
                'rank' => $user->rank,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'best_wpm' => round($user->best_wpm, 2),
                'best_accuracy' => round($user->best_accuracy, 2),
                'total_games' => $user->total_games ?? $user->total_attempts ?? 0,
                'avg_wpm' => isset($user->avg_wpm) ? round($user->avg_wpm, 2) : null,
                'avg_accuracy' => isset($user->avg_accuracy) ? round($user->avg_accuracy, 2) : null,
            ];
        })->toArray();
    }
}

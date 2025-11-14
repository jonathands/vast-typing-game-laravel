<?php

namespace App\Services;

use App\Http\Resources\TextPassageResource;
use App\Models\TextPassage;

class GameDataService
{
    public function __construct(
        protected RankingService $rankingService
    ) {}

    public function getAllGames(int $page = 1, int $perPage = 10): array
    {
        $passages = TextPassage::query()->paginate($perPage, ['*'], 'page', $page);
        $rankings = $this->rankingService->getTopRankings(10);

        return [
            'passages' => [
                'data' => TextPassageResource::collection($passages->items()),
                'pagination' => [
                    'current_page' => $passages->currentPage(),
                    'last_page' => $passages->lastPage(),
                    'per_page' => $passages->perPage(),
                    'total' => $passages->total(),
                ],
            ],
            'ranking' => $this->rankingService->formatRanking($rankings),
        ];
    }

    public function getRandomGame(): array
    {
        $passage = TextPassage::query()->inRandomOrder()->first();

        if (!$passage) {
            return [
                'passage' => null,
                'ranking' => [],
            ];
        }

        $rankings = $this->rankingService->getRankingForPassage($passage->id, 10);

        return [
            'passage' => new TextPassageResource($passage),
            'ranking' => $this->rankingService->formatRanking($rankings),
        ];
    }

    public function getGameById(int $id): array
    {
        $passage = TextPassage::query()->findOrFail($id);
        $rankings = $this->rankingService->getRankingForPassage($id, 10);

        return [
            'passage' => new TextPassageResource($passage),
            'ranking' => $this->rankingService->formatRanking($rankings),
        ];
    }
}

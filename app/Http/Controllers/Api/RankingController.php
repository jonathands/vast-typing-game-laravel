<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RankingResource;
use App\Services\RankingService;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function __construct(
        protected RankingService $rankingService
    ) {}

    public function general(Request $request)
    {
        $limit = $request->query('limit', 10);
        $ranking = $this->rankingService->getGeneralRanking($limit);

        return RankingResource::collection($ranking);
    }

    public function accuracy(Request $request)
    {
        $limit = $request->query('limit', 10);
        $ranking = $this->rankingService->getAccuracyRanking($limit);

        return RankingResource::collection($ranking);
    }

    public function speed(Request $request)
    {
        $limit = $request->query('limit', 10);
        $ranking = $this->rankingService->getSpeedRanking($limit);

        return RankingResource::collection($ranking);
    }
}

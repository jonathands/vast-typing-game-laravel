<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GameDataService;
use Illuminate\Http\Request;

class GameDataController extends Controller
{
    public function __construct(
        protected GameDataService $gameDataService
    ) {}

    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        $data = $this->gameDataService->getAllGames($page, $perPage);

        return response()->json($data);
    }

    public function random()
    {
        $data = $this->gameDataService->getRandomGame();

        if (!$data['passage']) {
            return response()->json(['message' => 'No passages available'], 404);
        }

        return response()->json($data);
    }

    public function show(int $id)
    {
        $data = $this->gameDataService->getGameById($id);

        return response()->json($data);
    }
}

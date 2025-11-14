<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResultResource;
use App\Services\GamePlayService;
use Illuminate\Http\Request;

class GamePlayController extends Controller
{
    public function __construct(
        protected GamePlayService $gamePlayService
    ) {}

    public function start(Request $request)
    {
        $request->validate([
            'text_passage_id' => 'required|exists:text_passages,id',
        ]);

        $gameData = $this->gamePlayService->startGame($request->text_passage_id);

        return response()->json($gameData);
    }

    public function submitWord(Request $request)
    {
        $request->validate([
            'word' => 'required|string',
            'expected_word' => 'required|string',
        ]);

        $result = $this->gamePlayService->submitWord(
            $request->word,
            $request->expected_word
        );

        return response()->json($result);
    }

    public function finish(Request $request)
    {
        $result = $this->gamePlayService->finishGame(
            $request->user(),
            $request->all()
        );

        return response()->json([
            'message' => 'Game finished successfully',
            'result' => new ResultResource($result),
        ], 201);
    }

    public function calculateStats(Request $request)
    {
        $request->validate([
            'user_input' => 'required|string',
            'expected_text' => 'required|string',
            'time_taken' => 'required|integer|min:1',
        ]);

        $stats = $this->gamePlayService->calculateGameStats(
            $request->user_input,
            $request->expected_text,
            $request->time_taken
        );

        return response()->json($stats);
    }
}

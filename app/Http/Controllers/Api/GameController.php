<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResultResource;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text_passage_id' => 'required|exists:text_passages,id',
            'user_input' => 'required|string',
            'wpm' => 'required|numeric|min:0',
            'accuracy' => 'required|numeric|min:0|max:100',
            'time_taken' => 'required|integer|min:1',
            'errors_count' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = Result::create([
            'user_id' => $request->user()->id,
            'text_passage_id' => $request->text_passage_id,
            'user_input' => $request->user_input,
            'wpm' => $request->wpm,
            'accuracy' => $request->accuracy,
            'time_taken' => $request->time_taken,
            'errors_count' => $request->errors_count,
            'completed_at' => now(),
        ]);

        return new ResultResource($result);
    }
}

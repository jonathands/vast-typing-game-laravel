<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResultResource;
use Illuminate\Http\Request;

class UserStatsController extends Controller
{
    public function history(Request $request)
    {
        $limit = $request->query('limit', 20);

        $results = $request->user()
            ->results()
            ->with('textPassage')
            ->orderByDesc('completed_at')
            ->limit($limit)
            ->get();

        return ResultResource::collection($results);
    }
}

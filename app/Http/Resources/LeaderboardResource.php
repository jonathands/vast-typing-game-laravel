<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'rank' => $this->resource['rank'] ?? null,
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
            ],
            'best_wpm' => $this->best_wpm,
            'best_accuracy' => $this->best_accuracy,
            'total_games' => $this->total_games,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RankingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'rank' => $this->rank ?? null,
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
            ],
            'avg_wpm' => round($this->avg_wpm ?? 0, 2),
            'avg_accuracy' => round($this->avg_accuracy ?? 0, 2),
            'total_games' => $this->total_games ?? 0,
            'score' => round($this->score ?? 0, 2),
        ];
    }
}

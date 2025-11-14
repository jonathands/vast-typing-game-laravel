<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'text_passage_id' => $this->text_passage_id,
            'wpm' => $this->wpm,
            'accuracy' => $this->accuracy,
            'time_taken' => $this->time_taken,
            'errors_count' => $this->errors_count,
            'completed_at' => $this->completed_at,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'text_passage' => $this->whenLoaded('textPassage', fn () => new TextPassageResource($this->textPassage)),
        ];
    }
}

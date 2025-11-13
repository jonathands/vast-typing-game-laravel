<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TextPassage extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'language',
        'word_count',
        'character_count',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'word_count' => 'integer',
            'character_count' => 'integer',
        ];
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }
}

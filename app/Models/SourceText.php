<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceText extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'title',
        'author',
        'language',
        'word_count',
        'character_count',
    ];

    protected function casts(): array
    {
        return [
            'word_count' => 'integer',
            'character_count' => 'integer',
        ];
    }
}

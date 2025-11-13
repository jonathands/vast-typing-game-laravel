<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'text_passage_id',
        'wpm',
        'accuracy',
        'time_taken',
        'errors_count',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'wpm' => 'decimal:2',
            'accuracy' => 'decimal:2',
            'time_taken' => 'integer',
            'errors_count' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function textPassage(): BelongsTo
    {
        return $this->belongsTo(TextPassage::class);
    }
}

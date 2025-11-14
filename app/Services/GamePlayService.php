<?php

namespace App\Services;

use App\Models\Result;
use App\Models\TextPassage;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GamePlayService
{
    public function startGame(int $textPassageId): array
    {
        $passage = TextPassage::query()->findOrFail($textPassageId);

        return [
            'game_id' => uniqid('game_', true),
            'passage' => [
                'id' => $passage->id,
                'text' => $passage->text,
                'word_count' => $passage->word_count,
                'character_count' => $passage->character_count,
            ],
            'started_at' => now()->toIso8601String(),
        ];
    }

    public function submitWord(string $word, string $expectedWord): array
    {
        $isCorrect = $word === $expectedWord;
        $errors = $this->calculateWordErrors($word, $expectedWord);

        return [
            'word' => $word,
            'expected' => $expectedWord,
            'is_correct' => $isCorrect,
            'errors' => $errors,
        ];
    }

    public function finishGame(User $user, array $gameData): Result
    {
        $validator = Validator::make($gameData, [
            'text_passage_id' => 'required|exists:text_passages,id',
            'user_input' => 'required|string',
            'wpm' => 'required|numeric|min:0',
            'accuracy' => 'required|numeric|min:0|max:100',
            'time_taken' => 'required|integer|min:1',
            'errors_count' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $result = Result::create([
            'user_id' => $user->id,
            'text_passage_id' => $gameData['text_passage_id'],
            'user_input' => $gameData['user_input'],
            'wpm' => $gameData['wpm'],
            'accuracy' => $gameData['accuracy'],
            'time_taken' => $gameData['time_taken'],
            'errors_count' => $gameData['errors_count'],
            'completed_at' => now(),
        ]);

        return $result->load('textPassage', 'user');
    }

    protected function calculateWordErrors(string $input, string $expected): int
    {
        $inputLength = strlen($input);
        $expectedLength = strlen($expected);
        $maxLength = max($inputLength, $expectedLength);

        if ($maxLength === 0) {
            return 0;
        }

        $errors = 0;
        for ($i = 0; $i < $maxLength; $i++) {
            $inputChar = $i < $inputLength ? $input[$i] : '';
            $expectedChar = $i < $expectedLength ? $expected[$i] : '';

            if ($inputChar !== $expectedChar) {
                $errors++;
            }
        }

        return $errors;
    }

    public function calculateGameStats(string $userInput, string $expectedText, int $timeTaken): array
    {
        $userWords = str_word_count($userInput);

        $correctChars = 0;
        $totalChars = strlen($expectedText);

        for ($i = 0; $i < min(strlen($userInput), strlen($expectedText)); $i++) {
            if ($userInput[$i] === $expectedText[$i]) {
                $correctChars++;
            }
        }

        $accuracy = $totalChars > 0 ? ($correctChars / $totalChars) * 100 : 0;
        $wpm = $timeTaken > 0 ? ($userWords / $timeTaken) * 60 : 0;
        $errorsCount = $totalChars - $correctChars + abs(strlen($userInput) - strlen($expectedText));

        return [
            'wpm' => round($wpm, 2),
            'accuracy' => round($accuracy, 2),
            'errors_count' => $errorsCount,
        ];
    }
}

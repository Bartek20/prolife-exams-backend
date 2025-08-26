<?php

namespace App\Utils\PDF\helpers;

class HelpersResult {
    public static function getGraphConfig($score): array {
        $percent = $score['exam'] / $score['max'] * 100;
        $passed = $score['exam'] >= $score['passing'];

        $color = $passed ? '#00e83b' : '#fc0404';
        $offset = 440 - 440 * $percent / 100;

        $dot = [
            'x' => 75 + 70 * cos(($percent * 3.6 - 90) * pi() / 180),
            'y' => 75 + 70 * sin(($percent * 3.6 - 90) * pi() / 180)
        ];

        $text = $passed ? 'Pozytywny' : 'Negatywny';

        return [
            'color' => $color,
            'offset' => $offset,
            'dot' => $dot,
            'text' => $text
        ];
    }

    public static function getQuestionPoints($question): array {
        $color = $question['answer'] != null && $question['answer'] == $question['correct'] ? '#00e83b' : '#fc0404';
        $points = $question['answer'] != null && $question['answer'] == $question['correct'] ? 1 : 0;

        return [
            'color' => $color,
            'points' => $points
        ];
    }
    public static function numberToLetter($num): string {
        if ($num < 1) return '';

        $result = '';
        while ($num > 0) {
            $num--;
            $char = chr($num % 26 + 65);
            $result = $char . $result;
            $num = intdiv($num, 26);
        }
        return $result;
    }
}

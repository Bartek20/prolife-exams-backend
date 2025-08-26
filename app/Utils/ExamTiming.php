<?php
namespace App\Utils\ExamTiming;
function getEndTime($start_time, $duration, $multiplier): DateTime {
    $duration = explode(':', $duration);
    return (new DateTime($start_time))
        ->add(new DateInterval(sprintf('PT%dH%dM%dS', $duration[0] * $multiplier, $duration[1] * $multiplier, $duration[2] * $multiplier)));
}
function getStatus($exam, $response): string {
    if ($response->end_time != null) {
        return 'exam-ended';
    }
    $end_time = getEndTime($response->start_time, $exam->duration, $exam->is_global_duration ? 1 : $exam->question_number)->add(new DateInterval(sprintf('PT%dH%dM%dS', 0, 0, 30)));
    if ($end_time < now()) {
        return 'exam-ended-time';
    }
    return 'exam-in-progress';
}

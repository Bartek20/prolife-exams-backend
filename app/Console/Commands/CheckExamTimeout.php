<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckExamTimeout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-exam-timeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marks unfinished exams as finished after timeout.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $res = DB::table('responses')
            ->where('status', 'in_progress')
            ->orderBy('start_time')
            ->get()
            ->groupBy('exam_id');

        foreach ($res as $examId => $responses) {
            $exam = DB::table('exams')->where('id', $examId)->first();
            if (!$exam) {
                continue;
            }

            list($hours, $minutes, $seconds) = explode(':', $exam->duration);

            if (!$exam->is_global_duration) {
                $hours = $hours * $exam->question_number;
                $minutes = $minutes * $exam->question_number;
                $seconds = $seconds * $exam->question_number;
            }

            foreach ($responses as $response) {
                $startTime = Carbon::parse($response->start_time);
                $endTime = $startTime->copy()->addHours((int)$hours)->addMinutes((int)$minutes)->addSeconds((int)$seconds);
                $timeMargin = $endTime->copy()->addSeconds(30);

                if ($timeMargin->greaterThan(now())) {
                    break;
                }

                DB::table('responses')
                    ->where('uuid', $response->uuid)
                    ->update([
                        'end_time' => $endTime,
                        'status' => 'finished',
                    ]);
            }
        }
    }
}

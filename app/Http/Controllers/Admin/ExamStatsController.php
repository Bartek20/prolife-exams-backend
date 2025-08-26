<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Services\Admin\ExamStatsService;
use Illuminate\Http\Request;

class ExamStatsController extends Controller
{
    public function __construct(private ExamStatsService $examStatsService) {
    }

    public function show(Exam $exam, $year, $month = null, $day = null) {
        $stats = $this->examStatsService->getStats($exam, $year, $month, $day);

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}

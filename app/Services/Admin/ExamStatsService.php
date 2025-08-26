<?php

namespace App\Services\Admin;

use App\Models\Exam;
use App\Models\Statistic;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ExamStatsService {
    public function getStats(Exam $exam, $year, $month, $day) {
        $mode = 'week';
        if (!$day) {
            $day = 1;
            $mode = 'month';
        }
        if (!$month) {;
            $month = 1;
            $mode = 'year';
        }

        $date = Carbon::createFromFormat('Y-m-d', "$year-$month-$day");

        [$startDate, $endDate] = $this->getPeriod($mode, $date);

        $stats = $this->getData($exam, $startDate, $endDate);

        return $this->generateResponse($stats, $startDate, $endDate, $mode);
    }

    private function getPeriod($mode, $date) {
        switch ($mode) {
            case 'week':
                $startDate = $date->copy()->startOfWeek();
                $endDate = $date->copy()->endOfWeek();
                break;
            case 'month':
                $startDate = $date->copy()->startOfMonth();
                $endDate = $date->copy()->endOfMonth();
                break;
            case 'year':
                $startDate = $date->copy()->startOfYear();
                $endDate = $date->copy()->endOfYear();
                break;
        }

        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();

        return [$startDate, $endDate];
    }

    private function getData($exam, $start, $end) {
        return $exam->stats()->whereBetween('date', [$start, $end])->get();
    }

    private function generateResponse($stats, $start, $end, $mode) {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $data = collect($stats)->groupBy('date');

        switch ($mode) {
            case 'week':
                return $this->handleWeek($data, $start, $end);
            case 'month':
                return $this->handleMonth($data, $start, $end);
            case 'year':
                return $this->handleYear($data, $start, $end);
        }
    }

    private function handleWeek($data, $start, $end): array {
        $result = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateStr = $date->toDateString();
            $entry = $data->get($dateStr)?->first();

            $result[$dateStr] = [
                'created'   => $entry->created ?? 0,
                'completed' => $entry->completed ?? 0,
            ];
        }

        return $result;
    }
    private function handleMonth($data, $start, $end): array {
        $result = [];

        $current = $start->copy()->startOfMonth();
        $monthEnd = $start->copy()->endOfMonth();

        while ($current->lte($monthEnd)) {
            $weekStart = $current->copy();
            $weekEnd = $current->copy()->endOfWeek();

            if ($weekEnd->gt($monthEnd)) {
                $weekEnd = $monthEnd->copy();
            }

            $created = 0;
            $completed = 0;

            $period = CarbonPeriod::create($weekStart, $weekEnd);
            foreach ($period as $day) {
                $dayStr = $day->toDateString();
                if (isset($data[$dayStr])) {
                    foreach ($data[$dayStr] as $entry) {
                        $created += $entry->created;
                        $completed += $entry->completed;
                    }
                }
            }

            if ($weekStart->equalTo($weekEnd)) {
                $label = $weekStart->format('d/m/Y');
            } else {
                $label = $weekStart->format('d') . '-' . $weekEnd->format('d') . '/' . $weekEnd->format('m/Y');
            }

            $result[$label] = [
                'created'   => $created,
                'completed' => $completed,
            ];

            $current = $weekEnd->addDay();
        }

        return $result;
    }
    private function handleYear($data, $start, $end): array {
        $result = [];
        $monthlyData = [];

        foreach ($data->flatten(1) as $entry) {
            $month = Carbon::parse($entry->date)->format('Y-m');

            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = ['created' => 0, 'completed' => 0];
            }

            $monthlyData[$month]['created'] += $entry->created;
            $monthlyData[$month]['completed'] += $entry->completed;
        }

        for ($month = $start->copy()->startOfYear(); $month->lte($end); $month->addMonth()) {
            $monthStr = $month->format('Y-m');

            $result[$monthStr] = $monthlyData[$monthStr] ?? [
                'created' => 0,
                'completed' => 0,
            ];
        }

        return $result;
    }
}

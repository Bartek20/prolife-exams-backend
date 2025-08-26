<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:remove-demo-responses')->dailyAt('00:00');

Schedule::command('app:check-exam-timeout')
    ->everyMinute()
//    ->everyThirtySeconds() # Uncomment to increase time precision
    ->evenInMaintenanceMode()
    ->emailOutputOnFailure('kuzniar.bartlomiej20@gmail.com');

// Cleanup expired data
Schedule::command('sanctum:prune-expired --hours=0')->daily();
Schedule::command('model:prune --model=App\\Models\\Response')->dailyAt('00:00');

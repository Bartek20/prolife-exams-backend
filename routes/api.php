<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentryController;

Route::post('/sentry', [SentryController::class, 'handle']);
Route::get('/time', function () {
   $time = now();
    return response()->json([
       'success' => true,
       'time' => $time,
        'timestamp' => $time->timestamp
   ]);
});

foreach (glob(base_path('routes/api/*.php')) as $file) {
    require_once $file;
}

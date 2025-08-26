<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Exam\StateController;
use App\Http\Controllers\Exam\QuestionController;
use App\Http\Controllers\Exam\ReportController;

Route::group([
    'prefix' => 'exam',
], function () {
    Route::group([
        'controller' => StateController::class,
    ], function () {
        Route::get('/config/{code}', 'config')
            ->where('code', 'DEMO|[a-zA-Z]{12,}')
            ->middleware('throttle:exam-config');
        Route::get('/state', 'state')->middleware('sanctum:exam_state');
        Route::post('/start', 'start')->middleware('throttle:exam-start');
        Route::post('/finish', 'finish')->middleware('sanctum:exam_fill');
        Route::post('/restore', 'restore');
    });
    Route::group([
        'middleware' => ['sanctum:exam_fill'],
        'controller' => QuestionController::class,
    ], function () {
        Route::get('/question/{idx}', 'question');
        Route::post('/answer/{idx}', 'answer');
    });
    Route::group([
        'middleware' => ['sanctum:exam_fill'],
        'controller' => ReportController::class,
    ], function () {
        Route::get('/report', 'public');
        Route::post('/report', 'report');
    });
});

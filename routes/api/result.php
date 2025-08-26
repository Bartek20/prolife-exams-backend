<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ExamResultMiddleware;
use App\Http\Controllers\Exam\ResultController;

Route::group([
    'prefix' => 'results',
    'middleware' => ExamResultMiddleware::class,
    'controller' => ResultController::class,
], function () {
    Route::get('/{uuid}', 'getResult');
    Route::get('/{uuid}/download', 'getPDF');
});

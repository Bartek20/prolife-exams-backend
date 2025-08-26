<?php

use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\QuestionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ExamStatsController;
use App\Http\Controllers\Admin\ResponseController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['sanctum:admin_area'],
], function () {
    Route::group([
        'prefix' => 'exams',
    ], function () {
        Route::group([
            'controller' => ExamController::class,
        ], function () {
            Route::get('/', 'index');
            Route::get('/{exam}', 'show');
            Route::post('/', 'store');
            Route::put('/{exam}', 'update');
            Route::delete('/{exam}', 'destroy');
        });
        Route::get('/{exam}/stats/{year}/{month?}/{day?}', [ExamStatsController::class, 'show']);
        Route::group([
            'prefix' => '{exam}/responses',
            'controller' => ResponseController::class,
        ], function () {
            Route::get('/', 'index');
            Route::get('/list', 'list');

            Route::delete('/{response}', 'remove');
            Route::patch('/{response}', 'restore')->withTrashed();
        });
    });
    Route::group([
        'prefix' => 'questions',
        'controller' => QuestionController::class
    ], function () {
        Route::get('/', 'index');
        Route::get('/{question}', 'show');
        Route::post('/', 'store');
    });
    Route::group([
        'prefix' => 'certificates',
        'controller' => CertificateController::class
    ], function () {
        Route::get('/', 'create');
    });
});

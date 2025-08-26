<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorizationController;

Route::group([
    'prefix' => 'auth',
    'controller' => AuthorizationController::class,
], function () {
    Route::post('/login', 'tokenize')->middleware('throttle:login');
    Route::post('/logout', 'logout')->middleware('sanctum:admin_area|student_area');
    Route::get('/check', 'check')->middleware('sanctum:admin_area|student_area');
});

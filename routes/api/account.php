<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'account',
    'controller' => AccountController::class,
], function () {
    Route::post('/create', 'store')->middleware('throttle:account-create');
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\ResultApiController;
use App\Http\Controllers\Api\TimetableApiController;

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles');
    });

    Route::get('/students/{student}', [StudentApiController::class, 'show']);
    Route::get('/students/{student}/results', [ResultApiController::class, 'studentResults']);
    Route::get('/timetable/{semester}', [TimetableApiController::class, 'index']);
});

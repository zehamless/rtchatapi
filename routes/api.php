<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->group(function () {
Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::post('get-reset-token', [\App\Http\Controllers\AuthController::class, 'getResetToken'])->name('get-reset-token');
Route::post('reset-password', [\App\Http\Controllers\AuthController::class, 'resetPassword'])->name('reset-password')->middleware('ability:password-update');
});


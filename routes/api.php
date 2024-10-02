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
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
Route::apiResource('conversations', \App\Http\Controllers\ConversationController::class);
Route::get('messages/{conversation}', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
Route::post('messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
Route::delete('messages/{message}', [\App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');
});

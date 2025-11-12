<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\SalaryController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/production');

Route::middleware('guest')->group(static function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(static function (): void {
    Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    Route::post('/production/{productionItem}/complete', [ProductionController::class, 'complete'])
        ->middleware('role:supervisor')
        ->name('production.complete');

    Route::get('/salary', [SalaryController::class, 'index'])->name('salary.index');
});

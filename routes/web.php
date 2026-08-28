<?php

use App\Http\Controllers\AutomationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Main Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Automation API Endpoints
Route::prefix('api/automation')->group(function () {
    Route::post('/start', [AutomationController::class, 'startCategoryBatch'])->name('automation.start');
    Route::get('/stream/{batchId}', [AutomationController::class, 'streamBatchProgress'])->name('automation.stream');
    Route::get('/diff/{productId}', [AutomationController::class, 'getProductDiff'])->name('automation.diff');
    Route::post('/revert/{productId}', [AutomationController::class, 'revertProduct'])->name('automation.revert');
});

// Store & Settings Management
Route::prefix('api/stores')->group(function () {
    Route::post('/connect', [AutomationController::class, 'connectStore'])->name('stores.connect');
});

Route::prefix('api/settings')->group(function () {
    Route::post('/gemini', [AutomationController::class, 'saveApiKey'])->name('settings.gemini');
});

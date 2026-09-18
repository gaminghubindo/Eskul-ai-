<?php

use App\Http\Controllers\AutomationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Main Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Marketplace Registry & Schema
Route::get('/api/marketplaces/schema', [AutomationController::class, 'getMarketplaceSchema'])->name('marketplaces.schema');

// Automation API Endpoints
Route::prefix('api/automation')->group(function () {
    Route::post('/start', [AutomationController::class, 'startAutomation'])->name('automation.start');
    Route::post('/single/{productId}', [AutomationController::class, 'optimizeSingleProduct'])->name('automation.single');
    Route::get('/preview', [AutomationController::class, 'previewAutomationScope'])->name('automation.preview');
    Route::get('/stream/{batchId}', [AutomationController::class, 'streamBatchProgress'])->name('automation.stream');
    Route::get('/diff/{productId}', [AutomationController::class, 'getProductDiff'])->name('automation.diff');
    Route::post('/revert/{productId}', [AutomationController::class, 'revertProduct'])->name('automation.revert');
    Route::post('/batches/{batchId}/cancel', [AutomationController::class, 'cancelBatch'])->name('automation.batch.cancel');
    Route::post('/batches/{batchId}/update', [AutomationController::class, 'updateBatchStatus'])->name('automation.batch.update');
});

// Product Content Management (Individual Custom Description Edit)
Route::prefix('api/products')->group(function () {
    Route::post('/{productId}/update-content', [AutomationController::class, 'updateProductContent'])->name('products.updateContent');
});

// Store & Settings Management
Route::prefix('api/stores')->group(function () {
    Route::get('/', [AutomationController::class, 'listStores'])->name('stores.list');
    Route::post('/connect', [AutomationController::class, 'connectStore'])->name('stores.connect');
    Route::delete('/{storeId}', [AutomationController::class, 'disconnectStore'])->name('stores.disconnect');
});

Route::prefix('api/settings')->group(function () {
    Route::post('/gemini', [AutomationController::class, 'saveApiKey'])->name('settings.gemini');
});


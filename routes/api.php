<?php

use App\Http\Controllers\Api\DeadLettersController;
use App\Http\Controllers\Api\DeliveriesController;
use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\SubscriptionsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('healthz', HealthController::class)->name('v1.healthz');

    Route::middleware('api.key')->group(function () {
        Route::post('events', [EventsController::class, 'store'])->name('v1.events.store');
        Route::get('events', [EventsController::class, 'index'])->name('v1.events.index');
        Route::get('events/{id}', [EventsController::class, 'show'])->name('v1.events.show');

        Route::post('subscriptions', [SubscriptionsController::class, 'store'])->name('v1.subscriptions.store');
        Route::get('subscriptions', [SubscriptionsController::class, 'index'])->name('v1.subscriptions.index');
        Route::get('subscriptions/{id}', [SubscriptionsController::class, 'show'])->name('v1.subscriptions.show');
        Route::patch('subscriptions/{id}', [SubscriptionsController::class, 'update'])->name('v1.subscriptions.update');
        Route::delete('subscriptions/{id}', [SubscriptionsController::class, 'destroy'])->name('v1.subscriptions.destroy');
        Route::post('subscriptions/{id}/pause', [SubscriptionsController::class, 'pause'])->name('v1.subscriptions.pause');
        Route::post('subscriptions/{id}/resume', [SubscriptionsController::class, 'resume'])->name('v1.subscriptions.resume');
        Route::post('subscriptions/{id}/rotate-secret', [SubscriptionsController::class, 'rotateSecret'])->name('v1.subscriptions.rotate');

        Route::get('deliveries', [DeliveriesController::class, 'index'])->name('v1.deliveries.index');
        Route::get('deliveries/{id}', [DeliveriesController::class, 'show'])->name('v1.deliveries.show');
        Route::post('deliveries/{id}/retry', [DeliveriesController::class, 'retry'])->name('v1.deliveries.retry');

        Route::get('dead-letters', [DeadLettersController::class, 'index'])->name('v1.dead-letters.index');
        Route::post('dead-letters/{id}/replay', [DeadLettersController::class, 'replay'])->name('v1.dead-letters.replay');
    });
});

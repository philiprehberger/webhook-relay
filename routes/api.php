<?php

use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\HealthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('healthz', HealthController::class)->name('v1.healthz');

    Route::middleware('api.key')->group(function () {
        Route::post('events', [EventsController::class, 'store'])->name('v1.events.store');
        Route::get('events', [EventsController::class, 'index'])->name('v1.events.index');
        Route::get('events/{id}', [EventsController::class, 'show'])->name('v1.events.show');
    });
});

<?php

use App\Http\Controllers\Account\ForecastsController;
use App\Http\Controllers\Agent\AccountChangeController;
use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\Agent\KpisController;
use App\Http\Controllers\Agent\OnboardingController;

Route::prefix('agent')->name('agent.')->namespace('App\Http\Controllers\Agent')->group(function () {
    Route::prefix('onboarding')->name('onboarding.')->controller(OnboardingController::class)->group(function () {
        Route::get('/{user}', 'form')->name('show');
        Route::post('/{user}', 'onboarding')->name('store');
    });

    Route::middleware(['auth', 'iam:agent'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('home');
        Route::get('/summary/chart', [DashboardController::class, 'chart'])->name('summary.chart');


        Route::post('account/select', AccountChangeController::class)->name('account.change');

        Route::get('/kpis', [KpisController::class, 'index'])->name('kpis');
        Route::get('/done-kpis', [KpisController::class, 'submitted'])->name('submitted_kpis');
        Route::get('/overdue-kpis', [KpisController::class, 'overdue'])->name('overdue_kpis');
        Route::get('/submit/{forecast}', [KpisController::class, 'form'])->name('kpi_submit_form');
        Route::post('/submit/{forecast}', [KpisController::class, 'submit'])->name('kpi_submit');
    });
    Route::get('/forecasts/{forecast}/download', [ForecastsController::class, 'downloadEvidence'])->name('forecasts.download');
});

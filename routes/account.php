<?php

use App\Enums\Permission;
use App\Http\Controllers\Account\AgentsController;
use App\Http\Controllers\Account\CategoriesController;
use App\Http\Controllers\Account\CompaniesController;
use App\Http\Controllers\Account\DashboardController;
use App\Http\Controllers\Account\DepartmentsController;
use App\Http\Controllers\Account\ForecastsController;
use App\Http\Controllers\Account\KpisController;
use App\Http\Controllers\Account\MasterTableController;
use App\Http\Controllers\Account\OnboardingController;
use App\Http\Controllers\Account\UsersController;

Route::name('account.')->namespace('App\Http\Controllers\Account')->group(function () {
    Route::prefix('onboarding')->name('onboarding.')->controller(OnboardingController::class)->group(function () {
        Route::get('/{user}', 'form')->name('show');
        Route::post('/{user}', 'onboarding')->name('store');
    });

    Route::middleware(['auth', 'iam:account'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/summary/chart', [DashboardController::class, 'chart'])->name('summary.chart')->middleware('can:'.Permission::DASHBOARD());


        Route::prefix('users')->name('users.')->controller(UsersController::class)->middleware('can:'.Permission::USERS())->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'form')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{user}', 'form')->name('edit');
            Route::put('/update/{user}', 'update')->name('update');
            Route::delete('/{user}', 'delete')->name('delete');
        });

        Route::prefix('companies')->name('companies.')->controller(CompaniesController::class)->middleware('can:'.Permission::COMPANIES())->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'form')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{company}', 'form')->name('edit');
            Route::put('/update/{company}', 'update')->name('update');
            Route::delete('/{company}', 'delete')->name('delete');
        });

        Route::prefix('departments')->name('departments.')->controller(DepartmentsController::class)->middleware('can:'.Permission::DEPARTMENTS())->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'form')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{department}', 'form')->name('edit');
            Route::put('/update/{department}', 'update')->name('update');
            Route::delete('/{department}', 'delete')->name('delete');
        });

        Route::prefix('agents')->name('agents.')->controller(AgentsController::class)->middleware('can:'.Permission::USERS())->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'form')->name('create');
            Route::post('/store', 'store')->name('store');

            Route::get('/edit/{user}', 'form')->name('edit');
            Route::put('/update/{user}', 'update')->name('update');

            Route::delete('/{user}', 'delete')->name('delete');
        });

        Route::prefix('categories')->name('categories.')->controller(CategoriesController::class)->middleware('can:'.Permission::KPIS_CATEGORIES())->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'form')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{category}', 'form')->name('edit');
            Route::put('/update/{category}', 'update')->name('update');
            Route::delete('/{category}', 'delete')->name('delete');
        });

        Route::prefix('kpis')->name('kpis.')->controller(KpisController::class)->middleware('can:'.Permission::KPIS())->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'form')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{kpi}', 'form')->name('edit');
            Route::put('/update/{kpi}', 'update')->name('update');
            Route::delete('/{kpi}', 'delete')->name('delete');

            Route::get('/byCategory/{category}', 'byCategory')->name('by_category');
        });

        Route::prefix('forecasts')->name('forecasts.')->controller(ForecastsController::class)->middleware('can:'.Permission::FORECASTS())->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/view/{forecast}', 'show')->name('view');
            Route::get('/create', 'form')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{forecast}', 'form')->name('edit');
            Route::put('/update/{forecast}', 'update')->name('update');
            Route::delete('/{forecast}', 'delete')->name('delete');

            Route::get('/sample', 'sample')->name('sample');
            Route::post('/import', 'import')->name('import');
        });
        Route::get('/forecasts/{forecast}/download', [ForecastsController::class, 'downloadEvidence'])->name('forecasts.download');

        Route::prefix('master')->name('master.')->controller(MasterTableController::class)->middleware('can:'.Permission::MASTER_TABLE())->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export', 'export')->name('export');

        });

    });

});

<?php

use App\Enums\Permission;
use App\Http\Controllers\Admin\AccountsController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;

Route::middleware(['auth', 'iam:admin'])
    ->prefix('admin')->name('admin.')
    ->namespace('App\Http\Controllers\Admin')
    ->group(function () {
        Route::get('/', function () {
            return view('dashboard');
        })->name('home');

        Route::prefix('users')->name('users.')->controller(AdminUsersController::class)->middleware('can:'.Permission::ADMIN_USERS())->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'form')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{user}', 'form')->name('edit');
            Route::put('/update/{user}', 'update')->name('update');
            Route::delete('/{user}', 'delete')->name('delete');
        });

        Route::prefix('accounts')->name('accounts.')->controller(AccountsController::class)->middleware('can:'.Permission::ADMIN_ACCOUNTS())->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'form')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{account}', 'form')->name('edit');
            Route::put('/update/{account}', 'update')->name('update');
            Route::delete('/{account}', 'delete')->name('delete');
        });
    });

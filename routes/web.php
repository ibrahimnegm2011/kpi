<?php

use App\Enums\UserType;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/account.php';

require __DIR__.'/agent.php';

require __DIR__.'/admin.php';

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        if(Auth::user()->type  == UserType::ADMIN) {
            return redirect(route('admin.home'));
        } elseif (Auth::user()->type == UserType::ACCOUNT) {
            return view('dashboard');
        } elseif (Auth::user()->type == UserType::AGENT) {
            return redirect(route('agent.home'));
        } else {
            abort(401, 'Invalid user. Please contact admin.');
        }
    })->name('home');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';

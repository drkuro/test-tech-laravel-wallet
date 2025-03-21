<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecurringTransfertController;
use App\Http\Controllers\SendMoneyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('/send-money', [SendMoneyController::class, '__invoke'])->name('send-money');

    Route::get('/recurring', [RecurringTransfertController::class, 'index'])->name('recurring');
    Route::get('/recurring/create', [RecurringTransfertController::class, 'create'])->name('recurring.create');
    Route::post('/recurring', [RecurringTransfertController::class, 'store'])->name('recurring.store');
    Route::delete('/recurring/{id}', [RecurringTransfertController::class, 'destroy'])->name('recurring.delete');
});

require __DIR__.'/auth.php';

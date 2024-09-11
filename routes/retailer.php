<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RetailerController;

Route::middleware(['auth', 'role:Retailer'])->group(function () {
    Route::get('/retailer/dashboard', [RetailerController::class, 'dashboard'])->name('retailer.dashboard');
});

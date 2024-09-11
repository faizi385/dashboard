<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LpController;

Route::get('/lp/dashboard', [LpController::class, 'dashboard'])->name('lp.dashboard');

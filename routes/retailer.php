<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\RetailerPortalController;

Route::middleware(['auth', 'role:Retailer'])->group(function () {
    Route::get('/retailer/dashboard', [RetailerController::class, 'dashboard'])->name('retailer.dashboard');
    //     Route::get('/manage-info', [RetailerPortalController::class, 'manageInfo'])->name('retailer.manageInfo');
    //     Route::get('/profile/edit', [RetailerPortalController::class, 'editProfile'])->name('retailer.editProfile');
    //     Route::put('/profile/update', [RetailerPortalController::class, 'updateProfile'])->name('retailer.updateProfile');
    // // In routes/retailer.php
    // Route::get('/add-location', [RetailerPortalController::class, 'addLocation'])->name('retailer.addLocation');

    
    //     Route::post('/address/store', [RetailerPortalController::class, 'storeAddress'])->name('retailer.address.store');
});

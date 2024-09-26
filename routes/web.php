<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LpController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LpLogController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\ManageInfoController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProvinceLogController;
use App\Http\Controllers\RetailerLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:Super Admin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('roles', RoleController::class);
});
// Route::prefix('manage-info')->name('manage-info.')->group(function () {
//     Route::get('/', [ManageInfoController::class, 'index'])->name('index');
//     Route::post('/', [ManageInfoController::class, 'update'])->name('update');
// });
Route::resource('users', UserController::class)->middleware('auth');
Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile');
Route::get('/settings', [App\Http\Controllers\UserController::class, 'settings'])->name('settings');
Route::post('/settings', [UserController::class, 'updateSettings'])->name('settings.update');


Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
Route::resource('permissions', PermissionController::class);

// Provinces and Province Status
Route::resource('provinces', ProvinceController::class);
Route::patch('provinces/status/{province}', [ProvinceController::class, 'updateStatus'])->name('provinces.updateStatus');

// Retailers
Route::resource('retailers', RetailerController::class);
Route::get('/retailer/create', [RetailerController::class, 'create'])->name('retailer.create');
Route::post('/retailer/store', [RetailerController::class, 'store'])->name('retailer.store');
Route::get('/retailer-form/{token}', [RetailerController::class, 'showForm'])->name('retailer.fillForm');
Route::post('/retailer/submit-form', [RetailerController::class, 'submitForm'])->name('retailer.submitForm');

Route::get('/retailers', [RetailerController::class, 'index'])->name('retailer.index');
// Display the form to edit a retailer
Route::get('/retailer/{id}/edit', [RetailerController::class, 'edit'])->name('retailer.edit');

// Update a retailer
Route::put('/retailer/{id}', [RetailerController::class, 'update'])->name('retailer.update');

Route::delete('/retailers/{id}', [RetailerController::class, 'destroy'])->name('retailer.destroy');
// Add this line to define the route for showing retailer details
Route::get('/retailers/{id}', [RetailerController::class, 'show'])->name('retailer.show');

Route::get('/retailer/logs', [RetailerLogController::class, 'index'])->name('retailer.logs');

// Store a new address
Route::post('/retailer/{id}/address', [RetailerController::class, 'storeAddress'])->name('retailer.address.store');

// Show form to create a new address
Route::get('/retailer/{id}/address/create', [RetailerController::class, 'createAddress'])->name('retailer.address.create');

// Show form to edit an existing address
Route::get('/retailer/{id}/address/edit', [RetailerController::class, 'editAddress'])->name('retailer.address.edit');

// Update address
Route::put('/retailer/{id}/address', [RetailerController::class, 'updateAddress'])->name('retailer.address.update');

Route::get('/lp-management', [LpController::class, 'index'])->name('lp.management');

Route::get('/lps', [LpController::class, 'index'])->name('lp.index');
Route::get('/lp/create', [LpController::class, 'create'])->name('lp.create');
Route::post('/lp', [LpController::class, 'store'])->name('lp.store');
Route::get('/lp/{lp}/edit', [LpController::class, 'edit'])->name('lp.edit');
Route::put('/lp/{lp}', [LpController::class, 'update'])->name('lp.update');
Route::delete('/lp/{lp}', [LpController::class, 'destroy'])->name('lp.destroy');
// routes/web.php
Route::get('/lp/{id}/details', [LpController::class, 'show'])->name('lp.show');
Route::get('lp/logs', [LpLogController::class, 'index'])->name('lp.logs.index');


Route::get('/lp/complete/{id}', [LpController::class, 'completeForm'])->name('lp.completeForm');
Route::post('/lp/complete', [LpController::class, 'submitCompleteForm'])->name('lp.submitCompleteForm');

// Logs
Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
Route::get('/logs/{id}', [LogController::class, 'show'])->name('logs.show');

// Province Logs
Route::get('/province-logs', [ProvinceLogController::class, 'index'])->name('province-logs.index');


Route::middleware(['auth', 'role:LP'])->group(function () {
    Route::get('/manage-info', [ManageInfoController::class, 'index'])->name('manage-info.index');
    Route::post('/manage-info', [ManageInfoController::class, 'update'])->name('manage-info.update');
});


Route::get('/offers/create', [OfferController::class, 'create'])->name('offers.create');
Route::post('/offers/bulkUpload', [OfferController::class, 'bulkUpload'])->name('offers.bulkUpload');
// routes/web.php

Route::get('/offers/create', [OfferController::class, 'create'])->name('offers.create');
Route::post('/offers/store', [OfferController::class, 'store'])->name('offers.store');
Route::get('/offers/export', [OfferController::class, 'export'])->name('offers.export');
Route::post('offers/import', [OfferController::class, 'import'])->name('offers.import');



// Authentication routes
require __DIR__.'/auth.php';

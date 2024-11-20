<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LpController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LpLogController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarveoutController;
use App\Http\Controllers\OfferLogController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportLogController;
use App\Http\Controllers\ManageInfoController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\CarveoutLogController;
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
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:Super Admin']) // Ensure only Super Admins can access
    ->name('dashboard');

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
Route::middleware('auth')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::get('/profile', [UserController::class, 'profile'])->name('profile');
Route::get('/settings', [UserController::class, 'settings'])->name('settings');
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

Route::get('/manage-info', [ManageInfoController::class, 'index'])->name('manage-info.index')->middleware('auth');

Route::get('/offers/create', [OfferController::class, 'create'])->name('offers.create');
Route::post('/offers/bulkUpload', [OfferController::class, 'bulkUpload'])->name('offers.bulkUpload');
// routes/web.php

Route::get('/offers/create', [OfferController::class, 'create'])->name('offers.create');
Route::post('/offers/store', [OfferController::class, 'store'])->name('offers.store');
Route::get('/offers/export', [OfferController::class, 'export'])->name('offers.export');
Route::post('offers/import', [OfferController::class, 'import'])->name('offers.import');
Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
Route::get('offers/{id}/edit', [OfferController::class, 'edit'])->name('offers.edit');
Route::put('offers/{id}', [OfferController::class, 'update'])->name('offers.update');
Route::delete('offers/{id}', [OfferController::class, 'destroy'])->name('offers.destroy');
Route::get('/offer-logs', [OfferLogController::class, 'index'])->name('offer.logs.index');

// Carveout Routes
Route::get('/carveouts', [CarveoutController::class, 'index'])->name('carveouts.index');
Route::get('/carveouts/create', [CarveoutController::class, 'create'])->name('carveouts.create');
Route::post('/carveouts', [CarveoutController::class, 'store'])->name('carveouts.store');
Route::get('/carveouts/{id}/edit', [CarveoutController::class, 'edit'])->name('carveouts.edit');
Route::put('/carveouts/{id}', [CarveoutController::class, 'update'])->name('carveouts.update');
Route::delete('/carveouts/{id}', [CarveoutController::class, 'destroy'])->name('carveouts.destroy');
Route::get('/lps/{id}', [LpController::class, 'show'])->name('lps.show');

Route::get('/carveouts', [CarveoutController::class, 'index'])->name('carveouts.index');
Route::get('/carveouts/{lp_id}', [CarveoutController::class, 'index'])->name('carveouts.index');
Route::get('/lp/carveouts', [CarveoutController::class, 'lpIndex'])->name('lp.carveouts.index')->middleware('role:LP');
Route::get('/carveouts/{id}', [CarveoutController::class, 'show'])->name('carveouts.show');

Route::get('carveout-logs', [CarveoutLogController::class, 'index'])->name('carveout.logs.index');


Route::get('/lp/products', [ProductController::class, 'viewProducts'])->name('lp.products');

// This route should be placed after the above route
Route::get('lp/{lp_id}/products', [ProductController::class, 'viewProducts'])->name('lp.products.by.id');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
Route::get('/products/variations/{lp_id}/{gtin}', [ProductController::class, 'showVariations'])->name('products.variations');

Route::get('/retailers/{retailer}/addresses', [RetailerController::class, 'getAddresses']);


Route::get('/retailers/{retailer}/reports/create', [ReportController::class, 'create'])->name('retailers.reports.create');
Route::post('/retailers/{retailer}/reports', [ReportController::class, 'store'])->name('retailers.reports.store');
Route::post('retailers/{retailer}/reports/import', [ReportController::class, 'import'])->name('retailers.reports.import');
Route::get('/report-logs', [ReportLogController::class, 'index'])->name('report.logs.index');
Route::get('/retailers/{retailer}/reports', [ReportController::class, 'index'])->name('retailers.reports.index');
Route::get('/super-admin/reports', [ReportController::class, 'index'])->name('super_admin.reports.index');
Route::get('/retailer/reports', [ReportController::class, 'index'])->middleware('auth')->name('retailer.reports.index');
Route::get('/report/{reportId}/download/{fileNumber}', [ReportController::class, 'downloadFile'])->name('report.download');
Route::get('reports/download/{reportId}/{fileNumber}', [ReportController::class, 'downloadFile'])->name('reports.downloadFile');
Route::get('/reports/{report_id}/export-clean-sheets', [ReportController::class, 'exportCleanSheets'])->name('reports.exportCleanSheets');
Route::get('/reports/{report_id}/export-statement', [ReportController::class, 'exportStatement'])->name('reports.exportStatement');
Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
// Route for creating a report in the retailer dashboard
// Route to view the statement
Route::get('/retailers/{retailer}/statement', [RetailerController::class, 'viewStatement'])->name('retailer.statement.view');
Route::get('superadmin/lp/{lp_id}/statement', [LpController::class, 'viewStatement'])->name('lp.statement.view');

Route::get('lp/statement/export/{lp_id}/{date}', [LpController::class, 'exportLpStatement'])->name('lp.statement.export');
Route::patch('/lp/{lp}/status', [LPController::class, 'updateStatus'])->name('lp.updateStatus');

Route::get('/account-created', function () {
    return view('account-created');
})->name('account.created');






// Authentication routes
require __DIR__.'/auth.php';

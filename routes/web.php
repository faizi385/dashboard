<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProvinceLogController;

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

Route::resource('users', UserController::class)->middleware('auth');


Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');


Route::resource('permissions', PermissionController::class);

Route::resource('provinces', ProvinceController::class);
Route::patch('provinces/status/{province}', [ProvinceController::class, 'updateStatus'])->name('provinces.updateStatus');

Route::resource('retailers', \App\Http\Controllers\RetailerController::class);
Route::get('/retailer/create', [RetailerController::class, 'create'])->name('retailer.create');
Route::post('/retailer/store', [RetailerController::class, 'store'])->name('retailer.store');


Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

Route::get('/province-logs', [ProvinceLogController::class, 'index'])->name('province-logs.index');
Route::get('/logs/{id}', [LogController::class, 'show'])->name('logs.show');

require __DIR__.'/auth.php';

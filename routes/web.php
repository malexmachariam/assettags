<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/batches', [App\Http\Controllers\BatchController::class, 'index'])->name('batches.index');
Route::post('/batches', [App\Http\Controllers\BatchController::class, 'store'])->name('batches.store');
Route::patch('/batches/{batch}/activate', [App\Http\Controllers\BatchController::class, 'activate'])->name('batches.activate');
Route::post('/batches/{batch}/assign-unassigned', [App\Http\Controllers\BatchController::class, 'assignUnassigned'])->name('batches.assign-unassigned');
Route::get('/batches/{batch}/tags/pdf', [App\Http\Controllers\BatchController::class, 'tagsPdf'])->name('batches.tags.pdf');

// Asset recording and QR code routes
Route::post('/assets/bulk-store', [App\Http\Controllers\AssetController::class, 'bulkStore'])->name('assets.bulk-store');
Route::get('/assets/upload', [App\Http\Controllers\AssetController::class, 'upload'])->name('assets.upload');
Route::post('/assets/import-serial-csv', [App\Http\Controllers\AssetController::class, 'importSerialCsv'])->name('assets.import-serial-csv');
Route::resource('assets', App\Http\Controllers\AssetController::class)->only(['create', 'store', 'show']);

// Allocation route
Route::post('/assets/{asset}/allocate', [App\Http\Controllers\AssetController::class, 'allocate'])->name('assets.allocate');
Route::get('/assets/{asset}/tag/pdf', [App\Http\Controllers\AssetController::class, 'tagPdf'])->name('assets.tag.pdf');
// Deallocate route
Route::patch('/assets/{asset}/deallocate', [App\Http\Controllers\AssetController::class, 'deallocate'])->name('assets.deallocate');

// Auth::routes();

// Route to trigger migrations (for cPanel environments without terminal access)
Route::get('/run-migrate', [App\Http\Controllers\MigrateController::class, 'migrate'])->middleware('auth');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Category routes
Route::resource('categories', App\Http\Controllers\CategoryController::class);

// Asset model routes
Route::resource('asset-models', App\Http\Controllers\AssetModelController::class)->except(['show', 'create', 'edit']);

// Asset Tag AJAX route
Route::get('/assets/{asset}/tag', [App\Http\Controllers\AssetController::class, 'tag'])->name('assets.tag');


// User management (super admin only)
Route::middleware(['auth', 'superadmin'])->group(function () {
	Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
	Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
	Route::patch('/users/{user}/activate', [App\Http\Controllers\UserController::class, 'activate'])->name('users.activate');
	Route::patch('/users/{user}/deactivate', [App\Http\Controllers\UserController::class, 'deactivate'])->name('users.deactivate');
	Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
	Route::patch('/users/{user}/role', [App\Http\Controllers\UserController::class, 'updateRole'])->name('users.updateRole');
});
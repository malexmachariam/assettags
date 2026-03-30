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
Route::get('/assets/{asset}/tag/pdf', [App\Http\Controllers\AssetController::class, 'tagPdf'])->name('assets.tag.pdf');

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Category routes
Route::resource('categories', App\Http\Controllers\CategoryController::class);

// Asset model routes
Route::resource('asset-models', App\Http\Controllers\AssetModelController::class)->except(['show', 'create', 'edit']);

// Asset Tag AJAX route
Route::get('/assets/{asset}/tag', [App\Http\Controllers\AssetController::class, 'tag'])->name('assets.tag');

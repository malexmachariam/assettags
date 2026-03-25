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

// Asset recording and QR code routes
Route::resource('assets', App\Http\Controllers\AssetController::class)->only(['create', 'store', 'show']);

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Category routes
Route::resource('categories', App\Http\Controllers\CategoryController::class);

// Asset model routes
Route::resource('asset-models', App\Http\Controllers\AssetModelController::class)->except(['show', 'create', 'edit']);

// Asset Tag AJAX route
Route::get('/assets/{asset}/tag', [App\Http\Controllers\AssetController::class, 'tag'])->name('assets.tag');

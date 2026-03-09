<?php

use App\Http\Controllers\CodeEsdController;
use App\Models\Schedule;
use App\Http\Middleware\AdminAuth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\TransactionController;

// Landing page
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/preview/{id}', [EntityController::class, 'preview'])->name('public.preview'); //preview page
Route::get('admin/proxy-awork', [EntityController::class, 'proxyAwork'])->name('admin.proxy.awork');//ambil api awork (set up dulu di env url & token)

// Dashboard (dilindungi oleh middleware 'auth')
Route::middleware(AdminAuth::class)->prefix('admin')->name('admin.')->group(function () {
    
    //route realnya di sini
    Route::get('/entities/create', [EntityController::class, 'create'])->name('entities.create');
    Route::get('/entities/edit/{id}', [EntityController::class, 'edit'])->name('entities.edit');
    Route::get('entities/{id}/copy', [EntityController::class, 'copy'])->name('entities.copy');
    Route::get('entities/{id}/download-qr', [EntityController::class, 'downloadQR'])->name('entities.download-qr');
    Route::apiResource('entities', EntityController::class);

    Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create');
    Route::get('/packages/edit/{id}', [PackageController::class, 'edit'])->name('packages.edit');
    Route::apiResource('packages', PackageController::class);
    

    Route::resource('category', CategoryController::class);
    Route::apiResource('items', ItemController::class);
    Route::apiResource('transactions', TransactionController::class);
    
    Route::get('/download-all-qr', [EntityController::class, 'downloadAllQR'])->name('entities.download-all-qr');
    // Route untuk memproses upload file Excel
    Route::post('/entities/import', [EntityController::class, 'import'])->name('entities.importExcel');

    Route::resource('code-esd', CodeEsdController::class);
});
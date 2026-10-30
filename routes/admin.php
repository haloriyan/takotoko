<?php

use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminStoreController;
use App\Http\Controllers\GeminiKeyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => "admin"], function () {
    Route::match(['get', 'post'], "login", [AdminController::class, 'login'])->name('admin.login');
    Route::group(['middleware' => "Admin"], function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('cms', [AdminController::class, 'cms'])->name('admin.cms');
        Route::get('logout', [AdminController::class, 'logout'])->name('admin.logout');

        Route::group(['prefix' => "master"], function () {
            Route::get('user', [AdminController::class, 'user'])->name('admin.master.user');
            Route::group(['prefix' => "stores"], function () {
                Route::post('store', [StoreController::class, 'store'])->name('admin.master.stores.store');
                Route::get('{id}/delete', [StoreController::class, 'delete'])->name('admin.master.stores.delete');
                
                Route::group(['prefix' => "{username}"], function () {
                    Route::get('product', [AdminStoreController::class, 'product'])->name('admin.master.stores.product');
                    Route::get('product/{id}/delete', [AdminStoreController::class, 'productDelete'])->name('admin.master.stores.product.delete');
                    Route::get('employee', [AdminStoreController::class, 'employee'])->name('admin.master.stores.employee');
                    Route::get('membership', [AdminStoreController::class, 'membership'])->name('admin.master.stores.membership');
                    Route::post('membership/{trxID}/status', [AdminStoreController::class, 'membershipStatus'])->name('admin.master.stores.membership.status');
                    Route::get('/', [AdminStoreController::class, 'index'])->name('admin.master.stores.detail');
                });
                Route::get('/', [AdminController::class, 'stores'])->name('admin.master.stores');
            });
        });

        Route::group(['prefix' => "settings"], function () {
            Route::group(['prefix' => "gemini-keys"], function () {
                Route::post('store', [GeminiKeyController::class, 'store'])->name('admin.settings.geminiKeys.store');
                Route::get('{id}/delete', [GeminiKeyController::class, 'delete'])->name('admin.settings.geminiKeys.delete');
                Route::get('/', [AdminController::class, 'geminiKeys'])->name('admin.settings.geminiKeys');
            });
        });
    });
});
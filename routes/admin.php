<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminStoreController;
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
    });
});
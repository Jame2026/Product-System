<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| Guest Routes (Login)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware('guest')
    ->group(function () {

        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store'])
            ->name('login.store');
    });

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/
Route::post('admin/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Authenticated Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {

        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        /*
        |-------------------------
        | Bulk Delete
        |-------------------------
        */

        // Category bulk delete
        Route::delete('categories/bulk-delete', [CategoryController::class, 'bulkDelete'])
            ->name('categories.bulkDelete');

        // Product bulk delete
        Route::delete('products/bulk-delete', [ProductController::class, 'bulkDelete'])
            ->name('products.bulkDelete');

        // User bulk delete (NEW)
        Route::delete('users/bulk-delete', [UserController::class, 'bulkDelete'])
            ->name('users.bulkDelete');

        /*
        |-------------------------
        | Resource Routes
        |-------------------------
        */

        Route::resource('categories', CategoryController::class)
            ->except(['show']);

        Route::resource('products', ProductController::class)
            ->except(['show']);

        // USERS RESOURCE (NEW)
        Route::resource('users', UserController::class)
            ->except(['show']);
    });
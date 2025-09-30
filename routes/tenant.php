<?php

use Illuminate\Support\Facades\Route;
use Ingenius\Storefront\Http\Controllers\StorefrontController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here is where you can register tenant-specific routes for your package.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the tenant middleware for multi-tenancy support.
|
*/

Route::middleware([
    'api',
])->prefix('api')->group(function () {
    Route::prefix('shop')->group(function () {
        Route::get('products', [StorefrontController::class, 'products'])
            ->name('shop.products')
            ->middleware('tenant.has.feature:list-shop-products');

        Route::get('categories', [StorefrontController::class, 'categories'])->name('shop.categories')->middleware('tenant.has.feature:list-shop-products');
    });
});

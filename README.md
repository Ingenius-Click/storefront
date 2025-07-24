# Ingenius Storefront

A Laravel package for storefront functionality in the Ingenius ecosystem.

## Installation

You can install the package via composer:

```bash
composer require ingenius/storefront
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=storefront-config
```

### Environment Variables

```
PRODUCT_MODEL=Ingenius\Products\Models\Product
```

> Note: For backward compatibility, `STOREFRONT_PRODUCT_MODEL` is still supported but `PRODUCT_MODEL` is preferred as it's used across all packages.

## Usage

### Routes

The package registers the following tenant routes:

```php
Route::middleware(['api'])->prefix('api')->group(function () {
    Route::prefix('shop')->group(function () {
        Route::get('products', [StorefrontController::class, 'products'])->name('shop.products');
    });
});
```

### Product Listing

The package provides functionality to list products for a storefront:

```php
use Ingenius\Storefront\Actions\ListShopProductsAction;

$products = app(ListShopProductsAction::class)->handle([
    'category_id' => 1,
    'search' => 'keyword',
    'sort' => 'created_at',
    'order' => 'desc',
    'per_page' => 20,
]);
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
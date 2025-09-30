<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can specify configuration options for the storefront package.
    |
    */

    'name' => 'Storefront',

    /*
    |--------------------------------------------------------------------------
    | Product Model Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can specify which class to use as the Product model when
    | displaying products in the storefront. This allows flexibility to change the
    | product model implementation without modifying the storefront logic.
    |
    */
    'product_model' => env('PRODUCT_MODEL', env('STOREFRONT_PRODUCT_MODEL', 'Ingenius\Products\Models\Product')),
    'category_model' => env('CATEGORY_MODEL', env('STOREFRONT_CATEGORY_MODEL', 'Ingenius\Products\Models\Category')),

];

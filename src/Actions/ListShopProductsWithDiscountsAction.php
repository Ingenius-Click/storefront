<?php

namespace Ingenius\Storefront\Actions;

use Ingenius\Core\Services\PackageHookManager;

class ListShopProductsWithDiscountsAction {

    public function __construct(
        protected PackageHookManager $hookManager
    ) {}

    public function handle(array $filters = []): array {
        $productModel = config('storefront.product_model');

        // Start with base query - if discounts package is installed,
        // it will modify this to filter products with discounts
        $query = $productModel::query();

        // Execute hook to get products with discounts
        // If discounts package is not installed, this will return the original query
        $query = $this->hookManager->execute('products.query.with_discounts', $query, [
            'filters' => $filters
        ]);

        // Apply readyForSale scope if available
        if (method_exists($productModel, 'scopeReadyForSale')) {
            $query->where(function($q) use ($productModel) {
                // Re-apply the readyForSale scope logic
                if (method_exists($productModel, 'scopeReadyForSale')) {
                    $instance = new $productModel;
                    $instance->scopeReadyForSale($q);
                }
            });
        }

        // Apply category filter if provided
        if (isset($filters['category_id']) && method_exists($productModel, 'categories')) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        $previouslyFilteredQuery = $query->clone();

        return table_handler_paginate_with_metadata($filters, $query, function ($filteredQuery) use ($previouslyFilteredQuery) {
            return [
                'min_price' => $previouslyFilteredQuery->min('sale_price'),
                'max_price' => $previouslyFilteredQuery->max('sale_price'),
            ];
        });
    }

}
<?php

namespace Ingenius\Storefront\Actions;

use Ingenius\Core\Services\PackageHookManager;

class ListShopProductsAction
{
    public function __construct(
        protected PackageHookManager $hookManager
    ) {}

    public function handle(array $filters = []): array
    {
        $productModel = config('storefront.product_model');

        if (!method_exists($productModel, 'scopeReadyForSale')) {
            $query = $productModel::query();
        } else {
            $query = $productModel::readyForSale();
        }

        if (isset($filters['category_id']) && method_exists($productModel, 'categories')) {
            $query->whereHas('categories', function ($query) use ($filters) {
                $query->where('categories.id', $filters['category_id']);
            });
        }

        $previouslyFilteredQuery = $query->clone();

        // Execute hook to get products with discounts if filters request it
        // If discounts package is not installed, this will return the original query
        if(isset($filters['with_discounts']) && $filters['with_discounts']) {
            $query = $this->hookManager->execute('products.query.with_discounts', $query, [
                'filters' => $filters
            ]);
        }

        if(isset($filters['best_selling']) && $filters['best_selling']) {
            $query = $this->hookManager->execute('products.query.best_selling', $query, [
                'filters' => $filters
            ]);
        }

        if(isset($filters['coming_soon']) && $filters['coming_soon'] && tenant()->hasFeature('coming-soon-product')) {
            $query = $this->hookManager->execute('products.query.coming_soon', $query, [
                'filters' => $filters
            ]);
        }

        return table_handler_paginate_with_metadata($filters, $query, function ($filteredQuery) use ($previouslyFilteredQuery) {
            return [
                'min_price' => $previouslyFilteredQuery->min('sale_price'),
                'max_price' => $previouslyFilteredQuery->max('sale_price'),
            ];
        }
        , app()->make(\Ingenius\Products\Services\ProductGenericTableHandler::class)
        );
    }
}

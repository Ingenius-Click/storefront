<?php

namespace Ingenius\Storefront\Actions;

class ListShopProductsAction
{
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

        return table_handler_paginate_with_metadata($filters, $query, function ($filteredQuery) {
            return [
                'min_price' => $filteredQuery->min('sale_price'),
                'max_price' => $filteredQuery->max('sale_price'),
            ];
        });
    }
}

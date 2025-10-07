<?php

namespace Ingenius\Storefront\Actions;

use Ingenius\Storefront\Transformers\ShopCategoryResource;

class ListShopCategoriesAction
{
    public function handle()
    {

        $categoryModel = config('storefront.category_model');

        $categories = $categoryModel::query()
            ->with('parent')
            ->whereNull('parent_id')
            ->whereHas('products', function ($query) {
                $productModel = config('storefront.product_model');

                if (method_exists($productModel, 'scopeReadyForSale')) {
                    $query->readyForSale();
                }
            })
            ->get();

        return ShopCategoryResource::collection($categories);
    }
}
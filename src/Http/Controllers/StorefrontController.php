<?php

namespace Ingenius\Storefront\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Ingenius\Products\Services\ProductPriceCacheService;
use Ingenius\Storefront\Actions\GetXBestSellingProductsAction;
use Ingenius\Storefront\Actions\ListShopCategoriesAction;
use Ingenius\Storefront\Actions\ListShopProductsAction;
use Ingenius\Storefront\Actions\ListShopProductsWithDiscountsAction;
use Ingenius\Storefront\Actions\MinMaxPricesAction;
use Ingenius\Storefront\Transformers\ShopCategoryResource;
use Ingenius\Storefront\Transformers\ShopProductCardResource;
use Ingenius\Storefront\Transformers\ShopProductOneResource;

class StorefrontController extends Controller
{
    public function __construct(
        protected ProductPriceCacheService $priceCache
    ) {}

    public function products(Request $request, ListShopProductsAction $listShopProductsAction): JsonResponse
    {
        $result = $listShopProductsAction->handle($request->all());

        // Warm the price cache for all products in this page before transformation
        $this->priceCache->warmBulkPrices($result['paginator']->items());

        $shopProducts = $result['paginator']->through(fn($product) => new ShopProductCardResource($product));

        return Response::api(
            data: $shopProducts,
            message: 'Products fetched successfully',
            params: ['metadata' => $result['metadata']]
        );
    }

    public function categories(Request $request, ListShopCategoriesAction $listCategoriesAction): JsonResponse
    {
        $categories = $listCategoriesAction->handle();

        return Response::api(data: ShopCategoryResource::collection($categories), message: 'Categories fetched successfully');
    }

    public function productOne(Request $request, $productible_id): JsonResponse {

        $productModel = config('storefront.product_model');

        $productible = $productModel::findOrFail($productible_id);

        return Response::api(data: new ShopProductOneResource($productible), message: __('Product show data fetched successfully'));
    }

    public function productsWithDiscounts(Request $request, ListShopProductsWithDiscountsAction $action): JsonResponse {
        $result = $action->handle($request->all());

        // Warm the price cache for all products in this page before transformation
        $this->priceCache->warmBulkPrices($result['paginator']->items());

        $shopProducts = $result['paginator']->through(fn($product) => new ShopProductCardResource($product));

        return Response::api(
            data: $shopProducts,
            message: 'Products with discounts fetched successfully',
            params: ['metadata' => $result['metadata']]
        );
    }

    public function bestSellingProducts(Request $request, GetXBestSellingProductsAction $action): JsonResponse {
        $limit = $request->input('limit', 10);
        $result = $action->handle($limit);

        // Warm the price cache for best-selling products before transformation
        $this->priceCache->warmBulkPrices($result->all());

        return Response::api(
            data: ShopProductCardResource::collection($result),
            message: 'Best-selling products fetched successfully'
        );
    }
}

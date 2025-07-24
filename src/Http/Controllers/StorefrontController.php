<?php

namespace Ingenius\Storefront\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ingenius\Storefront\Actions\ListShopProductsAction;
use Ingenius\Storefront\Transformers\ShopProductCardResource;

class StorefrontController extends Controller
{
    public function products(Request $request, ListShopProductsAction $listShopProductsAction): JsonResponse
    {
        $products = $listShopProductsAction->handle($request->all());

        $shopProducts = $products->through(fn($product) => new ShopProductCardResource($product));

        return response()->api(data: $shopProducts, message: 'Products fetched successfully');
    }
}

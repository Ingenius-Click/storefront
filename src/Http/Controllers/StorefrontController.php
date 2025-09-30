<?php

namespace Ingenius\Storefront\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Ingenius\Storefront\Actions\ListShopCategoriesAction;
use Ingenius\Storefront\Actions\ListShopProductsAction;
use Ingenius\Storefront\Transformers\ShopCategoryResource;
use Ingenius\Storefront\Transformers\ShopProductCardResource;

class StorefrontController extends Controller
{
    public function products(Request $request, ListShopProductsAction $listShopProductsAction): JsonResponse
    {
        $products = $listShopProductsAction->handle($request->all());

        $shopProducts = $products->through(fn($product) => new ShopProductCardResource($product));

        return Response::api(data: $shopProducts, message: 'Products fetched successfully');
    }

    public function categories(Request $request, ListShopCategoriesAction $listCategoriesAction): JsonResponse
    {
        $categories = $listCategoriesAction->handle();

        return Response::api(data: ShopCategoryResource::collection($categories), message: 'Categories fetched successfully');
    }
}

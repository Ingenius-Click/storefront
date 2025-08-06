<?php

namespace Ingenius\Storefront\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class ListShopProductsFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'list-shop-products';
    }

    public function getName(): string
    {
        return __('List shop products');
    }

    public function getGroup(): string
    {
        return __('Storefront');
    }

    public function getPackage(): string
    {
        return 'storefront';
    }

    public function isBasic(): bool
    {
        return true;
    }
}

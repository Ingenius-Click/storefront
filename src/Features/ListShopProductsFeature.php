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
        return 'List shop products';
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

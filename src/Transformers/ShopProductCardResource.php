<?php

namespace Ingenius\Storefront\Transformers;

use Ingenius\Core\Interfaces\IBaseProductibleData;
use Ingenius\Core\Interfaces\IInventoriable;
use Ingenius\Core\Interfaces\IPurchasable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopProductCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $data = [];

        if ($this->resource instanceof IBaseProductibleData) {
            $data['slug'] = $this->resource->getSlug();
            $data['sku'] = $this->resource->getSku();
            $data['description'] = $this->resource->getDescription();
            $data['images'] = $this->resource->images();
        }

        if ($this->resource instanceof IPurchasable) {
            $data['id'] = $this->resource->getId();
            $data['name'] = $this->resource->getName();
            $data['sale_price'] = $this->resource->getFinalPrice();
            $data['regular_price'] = $this->resource->getRegularPrice();
        }

        if ($this->resource instanceof IInventoriable) {
            $data['stock'] = $this->resource->getStock();
        }

        return $data;
    }
}

<?php

namespace Ingenius\Storefront\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ingenius\Core\Interfaces\IInventoriable;
use Ingenius\Core\Interfaces\IPurchasable;

class ShopProductVariationResource extends JsonResource {

    public function toArray(Request $request): array {
        $data = [];

        if($this->resource instanceof IPurchasable) {
            $data['id'] = $this->resource->getId();
            $data['name'] = $this->resource->getName();
            $data['sale_price'] = convert_currency($this->resource->getShowcasePrice());
            $data['regular_price'] = convert_currency($this->resource->getRegularPrice());
            $data['can_be_purchased'] = $this->resource->canBePurchased();
        }

        if($this->resource instanceof IInventoriable) {
            $data['stock'] = $this->resource->getStock();
        }

        if(isset($this->resource->images)) {
            $data['images'] = $this->resource->images;
        }

        if(isset($this->resource->attribute_options_ids)) {
            $data['attribute_options_ids'] = $this->resource->attribute_options_ids;
        }

        $data['is_default'] = $this->resource->is_default;
        $data['sku'] = $this->resource->sku;


        return $data;
    }

}
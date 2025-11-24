<?php

namespace Ingenius\Storefront\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Ingenius\Core\Interfaces\IPurchasable;
use Ingenius\Core\Services\PackageHookManager;

class ShopProductOneResource extends JsonResource {

    public function toArray(\Illuminate\Http\Request $request): array {

        $data = $this->resource->toArray();

        if ($this->resource instanceof IPurchasable) {
            $data['id'] = $this->resource->getId();
            $data['name'] = $this->resource->getName();
            $data['sale_price'] = $this->resource->getShowcasePrice();
            $data['regular_price'] = $this->resource->getRegularPrice();
        }

        // Apply product extensions
        $hookManager = App::make(PackageHookManager::class);

        $extraData = $hookManager->execute('product.array.extend', [],  [
            'product_id' => $this->resource->id,
            'product_class' => get_class($this->resource),
            'base_price' => $this->resource->sale_price,
            'regular_price' => $this->resource->regular_price,
        ]);

        // Return the data needed for productible show page. Remember use the productible interfaces to prevent hard relations.
        return [
            ... $data,
            ... $extraData
        ];
    }

}
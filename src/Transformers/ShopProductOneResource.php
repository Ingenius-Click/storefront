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

            // Prices are stored in base currency, convert to current currency
            $data['sale_price'] = convert_currency($this->resource->getShowcasePrice());
            $data['regular_price'] = convert_currency($this->resource->getRegularPrice());
            $data['can_be_purchased'] = $this->resource->canBePurchased();
        }

        // Add coming soon fields if they exist on the resource
        if (isset($this->resource->coming_soon) && tenant()->hasFeature('coming-soon-product')) {
            $data['coming_soon'] = $this->resource->coming_soon;
        }

        if (isset($this->resource->available_from) && tenant()->hasFeature('coming-soon-product')) {
            $data['available_from'] = $this->resource->available_from;
        }

        // Apply product extensions
        $hookManager = App::make(PackageHookManager::class);

        $extraData = $hookManager->execute('product.array.extend', [],  [
            'product_id' => $this->resource->id,
            'product_class' => get_class($this->resource),
            'base_price' => $this->resource->sale_price,
            'regular_price' => $this->resource->regular_price,
        ]);

        // Add currency metadata
        $data['currency'] = get_currency_metadata();

        // Return the data needed for productible show page. Remember use the productible interfaces to prevent hard relations.
        return [
            ... $data,
            ... $extraData
        ];
    }

}
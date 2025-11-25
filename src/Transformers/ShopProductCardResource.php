<?php

namespace Ingenius\Storefront\Transformers;

use Ingenius\Core\Interfaces\IBaseProductibleData;
use Ingenius\Core\Interfaces\IInventoriable;
use Ingenius\Core\Interfaces\IPurchasable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Ingenius\Core\Services\PackageHookManager;
use Ingenius\Products\Services\ProductExtensionManager;

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
            $data['sale_price'] = $this->resource->getShowcasePrice();
            $data['regular_price'] = $this->resource->getRegularPrice();
            $data['can_be_purchased'] = $this->resource->canBePurchased();
        }

        if ($this->resource instanceof IInventoriable) {
            $data['stock'] = $this->resource->getStock();
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

        return array_merge($data, $extraData);
    }
}

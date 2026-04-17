<?php

namespace Ingenius\Storefront\Actions;

use Ingenius\Products\Models\AttributeOption;

class DynamicAttributesAvailabilityAction {


    /**
     * Summary of __invoke
     * @param mixed $product
     * @param array<AttributeOption> $selectedAttributesOptions
     * @return void
     */
    public function __invoke($product, array $selectedAttributesOptions): array 
    {
        if($product->variants()->count() === 0) {
            return [];
        }

        $selectedAttributesIds = array_map(function($option) {
            return $option->attribute_id;
        }, $selectedAttributesOptions);

        $productAttributes = $product->attributes()->with('options')->get();

        //Check that the selected options are valid for the product
        foreach($selectedAttributesOptions as $option) {
            if(!$productAttributes->where('id', $option->attribute_id)->first()?->options->where('id', $option->id)->first()) {
                return [];
            }
        }


        $attributesLeft = $productAttributes->filter(function($attribute) use ($selectedAttributesIds) {
            return !in_array($attribute->id, $selectedAttributesIds);
        });

        //Check availability of options for the remaining attributes based on the selected options
        $availableAttributeOptions = [];

        foreach($attributesLeft as $attribute) {
            foreach($attribute->options as $option) {
                $arrIds = array_map(function($option) {
                    return $option->id;
                }, array_merge($selectedAttributesOptions, [$option]));
                $isAvailable = $product->variants()->whereHas('attributeOptions', function($query) use ($option, $arrIds) {
                    $query->whereIn('attribute_option_id', $arrIds);
                }, '=', count($arrIds))->exists();

                if($isAvailable) {
                    $availableAttributeOptions[] = $option;
                }
            }
        }

        return $availableAttributeOptions;
    }


}
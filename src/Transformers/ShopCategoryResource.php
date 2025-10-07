<?php

namespace Ingenius\Storefront\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent' => $this->parent ? [
                'id' => $this->parent->id,
                'name' => $this->parent->name,
                'slug' => $this->parent->slug,
            ] : null,
            'children' => $this->children?->map(fn($child) => [
                'id' => $child->id,
                'name' => $child->name,
                'slug' => $child->slug,
            ]),
            'images' => $this->images,
        ];
    }
}
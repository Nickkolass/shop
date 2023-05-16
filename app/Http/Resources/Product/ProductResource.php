<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'saler_id' => $this->saler_id,
            'saler' => $this->saler,
            'option_values' => $this->optionValues,
            'category' => $this->category,
            'property_values' => $this->propertyValues,
            'product_types' => ProductTypesResource::collection($this->productTypes)->resolve(),
        ];

    }
}

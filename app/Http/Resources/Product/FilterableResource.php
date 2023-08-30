<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class FilterableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'option_values' => $this->resource['optionValues'],
            'property_values' => $this->resource['propertyValues'],
            'prices' => $this->resource['prices'],
            'salers' => $this->resource['salers'],
            'tags' => $this->resource['tags'],
        ];
    }
}

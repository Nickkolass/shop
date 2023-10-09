<?php

namespace App\Http\Resources\Product;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class FilterableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<mixed>|Arrayable|JsonSerializable
     */
    public function toArray($request): array|Arrayable|JsonSerializable
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

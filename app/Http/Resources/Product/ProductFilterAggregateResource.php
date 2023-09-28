<?php

namespace App\Http\Resources\Product;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ProductFilterAggregateResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>|Arrayable|JsonSerializable
     */
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        return [
            'product_types' => new ProductTypeCollection($this->resource['productTypes']),
            'paginate' => $this->resource['paginate'],
            'filter' => $this->resource['filter'],
            'filterable' => new FilterableResource($this->resource['filterable']),
            'category' => $this->resource['category'],
        ];
    }
}

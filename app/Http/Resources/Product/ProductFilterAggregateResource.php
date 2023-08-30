<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductFilterAggregateResource extends JsonResource
{
    protected $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
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

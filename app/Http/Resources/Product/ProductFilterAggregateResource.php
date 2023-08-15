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
            'product_types' => $this['productTypes']->setCollection(collect(ProductTypeResource::collection($this['productTypes'])->resolve())),
            'paginate' => $this['paginate'],
            'filter' => $this['filter'],
            'filterable' => new FilterableResource($this['filterable']),
            'category' => $this['category'],
            'liked_ids' => $this['liked_ids'] ?? [],
        ];
    }
}

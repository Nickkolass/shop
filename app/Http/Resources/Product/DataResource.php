<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Order\FilterableResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DataResource extends JsonResource
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
            'productTypes' => $this['productTypes']->setCollection(collect(ProductTypeResource::collection($this['productTypes'])->resolve())),
            'paginate' => $this['paginate'],
            'filter' => $this['filter'],
            'filterable' => new FilterableResource($this['filterable']),
            'category' => $this['category'],
            'liked_ids' => $this['liked_ids'] ?? [],
        ];
    }
}

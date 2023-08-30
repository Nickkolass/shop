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
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'rating' => $this->resource->rating,
            'count_rating' => $this->resource->countRating,
            'count_comments' => $this->resource->countComments,
            'product_types' => ShowProductTypesResource::collection($this->resource->productTypes)->resolve(),
        ];
    }
}

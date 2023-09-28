<?php

namespace App\Http\Resources\Product;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ProductResource extends JsonResource
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
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'rating' => $this->resource->rating,
            'count_rating' => $this->resource->countRating,
            'count_comments' => $this->resource->countComments,
            'product_types' => ShowProductTypesResource::collection($this->resource->productTypes)->resolve(),
        ];
    }
}

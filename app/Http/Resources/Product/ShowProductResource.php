<?php

namespace App\Http\Resources\Product;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ShowProductResource extends JsonResource
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
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'option_values' => $this->resource->optionValues,
            'category' => $this->resource->category,
            'property_values' => $this->resource->propertyValues,
            'product_types' => ShowProductTypesResource::collection($this->resource->productTypes)->resolve(),
            'rating' => $this->resource->rating,
            'count_rating' => $this->resource->count_rating,
            'count_comments' => $this->resource->count_comments,
            'commentable' => !$this->resource->ratingAndComments->contains('user_id', '=', auth('api')->id()),
            'rating_and_comments' => RatingAndCommentsResource::collection($this->resource->ratingAndComments)->resolve(),
        ];

    }
}

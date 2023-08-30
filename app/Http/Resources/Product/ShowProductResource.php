<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowProductResource extends JsonResource
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
            'description' => $this->resource->description,
            'option_values' => $this->resource->optionValues,
            'category' => $this->resource->category,
            'property_values' => $this->resource->propertyValues,
            'product_types' => ShowProductTypesResource::collection($this->resource->productTypes)->resolve(),
            'rating' => $this->resource->rating,
            'count_rating' => $this->resource->countRating,
            'count_comments' => $this->resource->countComments,
            'commentable' => !$this->resource->rating_and_comments_exists,
            'rating_and_comments' => RatingAndCommentsResource::collection($this->resource->ratingAndComments)->resolve(),
        ];

    }
}

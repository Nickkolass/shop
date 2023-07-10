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
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'option_values' => $this->optionValues,
            'category' => $this->category,
            'property_values' => $this->propertyValues,
            'product_types' => ProductTypesResource::collection($this->productTypes)->resolve(),
            'rating' => $this->rating,
            'countRating' => $this->countRating,
            'countComments' => $this->countComments,
            'commentable' => !$this->rating_and_comments_exists,
            'ratingAndComments' => RatingAndCommentsResource::collection($this->ratingAndComments)->resolve(),
        ];

    }
}

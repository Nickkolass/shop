<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class RatingAndCommentsResource extends JsonResource
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
            'message' => $this->resource->message,
            'rating' => $this->resource->rating,
            'user' => $this->resource->user,
            'comment_images' => $this->resource->commentImages->pluck('file_path'),
            'created_at' => $this->resource->created_at->diffForHumans(),
        ];
    }
}

<?php

namespace App\Http\Resources\Product;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class RatingAndCommentsResource extends JsonResource
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
            'message' => $this->resource->message,
            'rating' => $this->resource->rating,
            'user' => $this->resource->user,
            'comment_images' => $this->resource->commentImages->pluck('file_path'),
            'created_at' => $this->resource->created_at->diffForHumans(),
        ];
    }
}

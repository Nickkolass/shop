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
            'message' => $this->message,
            'rating' => $this->rating,
            'user' => $this->user->name,
            'commentImages' => $this->commentImages->pluck('file_path'),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}

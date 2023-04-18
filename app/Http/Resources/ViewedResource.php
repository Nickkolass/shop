<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ViewedResource extends JsonResource
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
            'count' => $this->count,
            'is_published' => $this->is_published,
            'price' => $this->price,
            'preview_image' => $this->preview_image,
            'product_images' => $this->productImages,
            'category' => $this->category,
            'option_values' => $this->optionValues,
            'inCart' => $this->inCart,
        ];
    }
}
<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Category\CategoryResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexProductResource extends JsonResource
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
            'descriprion' => $this->descriprion, 
            'content' => $this->content, 
            'image_url' => $this->imageUrl, 
            'price' => $this->price, 
            'count' => $this->count, 
            'is_published' => $this->is_published, 
            'category' => new CategoryResource($this->category), 
        ];
           
    }
}

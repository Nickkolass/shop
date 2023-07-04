<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductTypeResource extends JsonResource
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
            'product_id' => $this->product_id,
            'price' => $this->price,
            'count' => $this->count,
            'is_published' => (bool) $this->is_published,
            'preview_image' => $this->preview_image,
            'product_images' => $this->productImages->pluck('file_path'),
            'option_values' => $this->optionValues,
            'product' => ProductResource::make($this->product)->resolve(),
        ];
    }
}

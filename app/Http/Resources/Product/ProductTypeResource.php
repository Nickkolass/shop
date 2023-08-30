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
            'id' => $this->resource->id,
            'product_id' => $this->resource->product_id,
            'price' => $this->resource->price,
            'count' => $this->resource->count,
            'is_published' => (bool) $this->resource->is_published,
            'preview_image' => $this->resource->preview_image,
            'product_images' => $this->resource->productImages->pluck('file_path'),
            'option_values' => $this->resource->optionValues,
            'likeable' => !$this->resource->liked_exists,
            'product' => $request->route()->getName() == 'back.api.products.filter'
                ? ProductResource::make($this->resource->product)->resolve()
                : ShowProductResource::make($this->resource->product)->resolve()
        ];
    }
}

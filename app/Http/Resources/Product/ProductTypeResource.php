<?php

namespace App\Http\Resources\Product;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ProductTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>|Arrayable|JsonSerializable
     */
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        return [
            'id' => $this->resource->id,
            'product_id' => $this->resource->product_id,
            'price' => $this->resource->price,
            'count' => $this->resource->count,
            'is_published' => (bool)$this->resource->is_published,
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

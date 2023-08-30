<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'is_published' => (bool)$this->resource->is_published,
            'preview_image' => $this->resource->preview_image,
            'option_values' => $this->resource->optionValues,
            'title' => $this->resource->product->title,
            'amount' => (int)$this->resource->amount,
            'total_price' => $this->resource->totalPrice,
        ];

    }
}

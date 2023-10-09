<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CartResource extends JsonResource
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

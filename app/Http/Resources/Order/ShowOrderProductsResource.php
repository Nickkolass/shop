<?php

namespace App\Http\Resources\Order;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ShowOrderProductsResource extends JsonResource
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
            'id' => $this->resource['productType_id'],
            'amount' => (int)$this->resource['amount'],
            'price' => $this->resource['price'],
            'title' => $this->resource['title'],
            'preview_image' => $this->resource['preview_image'],
            'option_values' => $this->resource['optionValues'],
        ];
    }
}

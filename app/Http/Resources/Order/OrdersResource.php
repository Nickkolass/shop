<?php

namespace App\Http\Resources\Order;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class OrdersResource extends JsonResource
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
            'product_types' => OrdersProductsResource::collection($this->resource->productTypes)->resolve(),
            'delivery' => $this->resource->delivery,
            'total_price' => $this->resource->total_price,
            'status' => $this->resource->status,
            'dispatch_time' => $this->resource->orderPerformers->max('dispatch_time'),
            'created_at' => $this->resource->created_at->toDateString(),
        ];
    }
}

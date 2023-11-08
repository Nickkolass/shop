<?php

namespace App\Http\Resources\Order;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ShowOrderPerformersResource extends JsonResource
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
            'id' => $this->resource['id'],
            'saler_id' => $this->resource['saler']['id'],
            'saler_name' => $this->resource['saler_name'],
            'status' => $this->resource['status'],
            'total_price' => $this->resource['total_price'],
            'dispatch_time' => $this->resource['dispatch_time']->toDateString(),
            'product_types' => ShowOrderProductsResource::collection($this->resource['productTypes'])->resolve(),
        ];

    }
}

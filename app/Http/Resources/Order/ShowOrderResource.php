<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class ShowOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'product_types' =>
                $request->route()->getName() == 'back.api.orders.index'
                    ? OrdersProductsResource::collection($this->resource->productTypes)->resolve()
                    : ShowOrderProductsResource::collection($this->resource->productTypes)->resolve(),
            'delivery' => $this->resource->delivery,
            'total_price' => $this->resource->total_price,
            'status' => $this->resource->status,
            'created_at' => $this->resource->created_at->toDateString(),
            'dispatch_time' => $this->resource->orderPerformers->max('dispatch_time'),
        ];
    }
}

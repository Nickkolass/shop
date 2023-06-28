<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowOrderResource extends JsonResource
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
            'productTypes' => is_object(current($this->productTypes)) ? ShowOrderProductsResource::collection($this->productTypes)->resolve() : OrdersProductsResource::collection($this->productTypes)->resolve(),
            'delivery' => $this->delivery,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateString(),
            'dispatch_time' => $this->orderPerformers->max('dispatch_time'),
        ];

    }
}

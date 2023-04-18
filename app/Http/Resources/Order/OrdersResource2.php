<?php

namespace App\Http\Resources\Order;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource2 extends JsonResource
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
            'user_id' => $this->user_id,
            'products' => OrdersProductsResource::collection($this->products)->resolve(),
            'delivery' => $this->delivery,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateString(),
            'dispatch_time' => $this->orderPerformers['0']->dispatch_time,
        ];
    }
}

<?php

namespace App\Http\Resources\Order;

use App\Models\OrderPerformer;
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
            'user_id' => $this->user_id,
            'products' => ShowOrderProductsResource::collection($this->products)->resolve(),
            'delivery' => $this->delivery,
            'total_price' => $this->total_price,
            'payment' => $this->payment,
            'payment_status' => $this->payment_status,
            'status' => $this->status,
            'dispatch_time' => $this->orderPerformers->max('dispatch_time'),
            'created_at' => $this->created_at->toDateString(),
        ];

    }
}

<?php

namespace App\Http\Resources\Order;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersProductsResource extends JsonResource
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
            'id' => $this['product_id'],
            'amount' => $this['amount'],
            'preview_image' => $this['preview_image'],
            'category' => $this['category'],
        ];
    }
}

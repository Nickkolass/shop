<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowOrderProductsResource extends JsonResource
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
            'id' => $this->resource['id'],
            'amount' => (int) $this->resource['amount'],
            'price' => $this->resource['price'],
            'option_values' => $this->resource['option_values'],
            'title' => $this->resource['product']['title'],
            'saler_id' => $this->resource['saler']['id'],
            'saler' => $this->resource['saler']['name'],
            'preview_image' => $this->resource['preview_image'],
            'status' =>  $this->resource['status'],
            'order_performer_id' => $this->resource['orderPerformer_id'],
        ];

    }
}

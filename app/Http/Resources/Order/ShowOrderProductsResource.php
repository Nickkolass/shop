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
            'id' => $this['id'],
            'amount' => (int) $this['amount'],
            'price' => $this['price'],
            'optionValues' => $this['option_values'],
            'title' => $this['product']['title'],
            'saler_id' => $this['saler']['id'],
            'saler' => $this['saler']['name'],
            'preview_image' => $this['preview_image'],
            'status' =>  $this['status'],
            'orderPerformer_id' => $this['orderPerformer_id'],
        ];


    }
}

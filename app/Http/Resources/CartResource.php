<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'title' => $this->title,
            'preview_image' => $this->preview_image,
            'price' => $this->price,
            'count' => $this->count,
            'amount' => $this->amount,
            'category' => $this->category->title,
            'saler' => $this->saler,
            'optionValues' => $this->optionValues,
            'cart_id' => $this->cart_id,
        ];
           
    }
}

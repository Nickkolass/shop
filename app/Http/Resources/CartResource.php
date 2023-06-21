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
            'product_id' => $this->product_id,
            'price' => $this->price,
            'count' => $this->count,
            'is_published' => $this->is_published,
            'preview_image' => $this->preview_image,
            'option_values' => $this->optionValues,
            'category' => $this->category->title,
            'title' => $this->product->title,
            'amount' => $this->amount,
            'totalPrice' => $this->totalPrice,
        ];

    }
}

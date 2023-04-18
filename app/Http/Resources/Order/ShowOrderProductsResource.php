<?php

namespace App\Http\Resources\Order;

use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\Product;
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
            'id' => $this->id,
            'amount' => $this->amount,
            'price' => $this->price,
            'optionValues' => $this->optionValues,
            'title' => $this->title,
            'saler_id' => $this->saler_id,
            'saler' => $this->saler->name,
            'preview_image' => $this->preview_image,
            'category' => $this->category->title,
            'status' =>  $this->orderPerformer['status'],
            'orderPerformer_id' => $this->orderPerformer['id'],
        ];


    }
}

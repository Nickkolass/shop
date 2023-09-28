<?php

namespace App\Http\Resources\Order;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ShowOrderProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>|Arrayable|JsonSerializable
     */
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        return [
            'id' => $this->resource['id'],
            'amount' => (int)$this->resource['amount'],
            'price' => $this->resource['price'],
            'option_values' => $this->resource['option_values'],
            'title' => $this->resource['product']['title'],
            'saler_id' => $this->resource['saler']['id'],
            'saler' => $this->resource['saler']['name'],
            'preview_image' => $this->resource['preview_image'],
            'status' => $this->resource['status'],
            'order_performer_id' => $this->resource['orderPerformer_id'],
        ];

    }
}

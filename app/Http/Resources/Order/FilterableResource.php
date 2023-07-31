<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class FilterableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'optionValues' => $this['optionValues'],
            'propertyValues' => $this['propertyValues'],
            'prices' => $this['prices'],
            'salers' => $this['salers'],
            'tags' => $this['tags'],
        ];
    }
}

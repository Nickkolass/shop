<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
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
            'products' => $this['products'],
            'category' => $this['category'],
            'tags' => $this['tags'],
            'colors' => $this['colors'],
            'salers' => $this['salers'],
            'prices' => ['minPrice' => $this['prices']['minPrice'],
                         'maxPrice' => $this['prices']['maxPrice']],
            'filter' => $this['request'],
        ];
    }
}

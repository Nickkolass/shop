<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
{
    protected $preserveKeys = true;
    
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
            'paginate' => $this['paginate'],
            'filter' => $this['filter'],
            'filterable' => $this['filterable'],
            'category' => $this['category'],
        ];
    }
}

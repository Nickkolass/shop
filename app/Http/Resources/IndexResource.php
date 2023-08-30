<?php

namespace App\Http\Resources;

use App\Http\Resources\Product\ProductTypeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
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
           'liked' => !empty($this->resource['liked']) ? ProductTypeResource::collection($this->resource['liked'])->resolve() : null,
           'viewed' => !empty($this->resource['viewed']) ? ProductTypeResource::collection($this->resource['viewed'])->resolve() : null,
        ];
    }
}

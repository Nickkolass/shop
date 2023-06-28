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
           'liked_ids' => !empty($this['liked']) ? $this['liked']->pluck('id')->flip() : '',
           'liked' => !empty($this['liked']) ? ProductTypeResource::collection($this['liked'])->resolve() : '',
           'viewed' => !empty($this['viewed']) ? ProductTypeResource::collection($this['viewed'])->resolve() : '',
        ];
    }
}

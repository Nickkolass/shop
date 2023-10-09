<?php

namespace App\Http\Resources;

use App\Http\Resources\Product\ProductTypeResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<mixed>|Arrayable|JsonSerializable
     */
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        return [
            'liked' => !empty($this->resource['liked']) ? ProductTypeResource::collection($this->resource['liked'])->resolve() : null,
            'viewed' => !empty($this->resource['viewed']) ? ProductTypeResource::collection($this->resource['viewed'])->resolve() : null,
        ];
    }
}

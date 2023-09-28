<?php

namespace App\Http\Resources\Product;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class ProductTypeCollection extends ResourceCollection
{
    /**
     * Сохранение пагинатора при создании коллекции ресурсов.
     *
     * @param Request $request
     * @return array<int|string, mixed>|Arrayable|JsonSerializable
     */
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        $data['data'] = ProductTypeResource::collection($this->resource)->resolve();
        $meta = $this->resource->setCollection(collect([]))->toArray();
        unset($meta['data']);
        foreach ($meta as $k => $v) $data[$k] = $v;
        return $data;
    }
}

<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrdersCollection extends ResourceCollection
{
    /**
     * Сохранение пагинатора при создании коллекции ресурсов.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data['data'] = ShowOrderResource::collection($this->resource)->resolve();
        $meta = $this->resource->setCollection(collect([]))->toArray();
        unset($meta['data']);
        foreach($meta as $k => $v) $data[$k] = $v;
        return $data;
    }
}

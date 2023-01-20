<?php

namespace App\Http\Resources\Product;
use App\Http\Resources\Product\ProductImageResource;
use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\Saler\SalerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return (
         [
            'id' => $this->id,
            'title' => $this->title, 
            'descriprion' => $this->descriprion, 
            'content' => $this->content, 
            'preview_image' => '/storage/'.$this->preview_image, 
            'price' => $this->price, 
            'count' => $this->count, 
            'is_published' => $this->is_published, 
            'saler' => new SalerResource($this->saler),
            'group' => new GroupResource($this->group), 
            'product_images' => ProductImageResource::collection($this->productImages), 
        ]);
    }
}
            // 'group_products' => ProductMiniResource::collection($products), 

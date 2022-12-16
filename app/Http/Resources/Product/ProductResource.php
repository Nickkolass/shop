<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Color\ColorResource;
use App\Models\Product;
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
        $products = Product::where('group_id', $this->group_id)->get();
        return [
            'id' => $this->id,
            'title' => $this->title, 
            'descriprion' => $this->descriprion, 
            'content' => $this->content, 
            'preview_image' => $this->preview_image, 
            'price' => $this->price, 
            'count' => $this->count, 
            'is_published' => $this->is_published, 
            'category' => new CategoryResource($this->category), 
            'product_images' => ProductImageResource::collection($this->productImages), 
            'group_products' => ProductMiniResource::collection($products), 
        ];
           
    }
}

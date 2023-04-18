<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductShowResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'preview_image' => $this->preview_image,
            'price' => $this->price,
            'count' => $this->count,
            'saler' => $this->saler,
            'group' => $this->group->products,
            'product_images' => $this->productImages,
            'option_values' => $this->optionValues,
            'property_values' => $this->propertyValues,
            'category' => $this->category,
            'inCart' => $this->inCart,

        ];
    }
}

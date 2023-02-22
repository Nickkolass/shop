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
            'product' => [
                'id' => $this['product']->id,
                'title' => $this['product']->title,
                'description' => $this['product']->description,
                'content' => $this['product']->content,
                'preview_image' => $this['product']->preview_image,
                'price' => $this['product']->price,
                'count' => $this['product']->count,
                'saler' => $this['product']->saler()->select('id', 'name')->get()->toArray(),
                'group' => $this['product']->group()->first()->products()->select('id', 'preview_image')->get()->toArray(),
                'product_images' => $this['product']->productImages()->pluck('file_path'),
                'colors' => $this['product']->colors()->select('colors.id', 'title')->get()->toArray(),      
            ],
            'category' => Category::where('title', $this['category'])->select('id', 'title', 'title_rus')->first()->toArray(),
        ];
    }
}

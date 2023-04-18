<?php

namespace App\Services\Product;

use App\Models\Group;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


class ProductService
{
    public $imageService;
    public $relationService;

    public function __construct(ImageService $imageService, RelationService $relationService)
    {
        $this->imageService = $imageService;
        $this->relationService = $relationService;
    }

    public function store($data)
    {

        $attach = $this->relationService->getAttach($data);
        $data['preview_image'] = $this->imageService->preview($data['preview_image']);

        DB::beginTransaction();
        try {

            $product = Product::firstOrCreate([
                'title' => $data['title']
            ], $data + ['saler_id' => auth()->id(), 'is_published' => 1]);

            $this->imageService->images($attach['productImages'], $product);
            unset($attach['productImages']);

            $this->relationService->attach($product, $attach);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }

    
    public function update(Product $product, $data)
    {
        
        $attach = $this->relationService->getAttach($data);
        empty($data['preview_image']) ?: $data['preview_image'] = $this->imageService->preview($data['preview_image'], $product->preview_image);
        $data = array_diff($data, $product->toArray());

        DB::beginTransaction();
        try {

            if (!empty($attach['productImages'])) {
                $this->imageService->images($attach['productImages'], $product, true);
                unset($attach['productImages']);
            }
            
            empty($attach) ?: $this->relationService->attach($product, $attach);
            $product->update($data);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public function delete(Product $product)
    {
        DB::beginTransaction();
        try {

            $this->imageService->deleteImages($product, true);
            $product->delete();
            empty($product->group_id) ?: Group::where('id', $product->group_id)->doesntHave('products')->delete();
            
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }

}

<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class Service
{
    public function store($data)
    {
        $data['saler_id'] = auth()->user()->id;
        DB::beginTransaction();
        try {
            $productImages = $data['product_images'];
            $tagsIds = $data['tags'];
            $colorsIds = $data['colors'];
            unset($data['tags'], $data['colors'], $data['product_images']);

            $data['preview_image'] = $data['preview_image']->storePublicly('preview_images', 'public');
            $product = Product::firstOrCreate([
                'title' => $data['title']
            ], $data);

            $product->tags()->attach($tagsIds);
            $product->colors()->attach($colorsIds);

            foreach ($productImages as $productImage) {
                $filePath = $productImage->storePublicly('images', 'public');
                ProductImage::create([
                    'file_path' => $filePath,
                    'url' => url('/storage/' . $filePath),
                    'size' => $productImage->getSize(),
                    'product_id' => $product->id,
                ]);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }

    public function update($data, $product)
    {
        DB::beginTransaction();
        try {
            if (isset($data['preview_image'])) {
                Storage::disk('public')->delete($product->preview_image);
                $data['preview_image'] = $data['preview_image']->storePublicly('preview_images', 'public');
            } else {
                $data['preview_image'] = $product->preview_image;
            }

            if (isset($data['product_images'])) {
                $oldproductImages = $product->productImages()->get('file_path');
                foreach ($oldproductImages as $oldproductImage){
                    Storage::disk('public')->delete($oldproductImage->file_path);
                }
                $product->productImages()->delete();

                foreach ($data['product_images'] as $productImage) {
                    $filePath = $productImage->storePublicly('images', 'public');
                    ProductImage::create([
                        'file_path' => $filePath,
                        'url' => url('/storage/' . $filePath),
                        'size' => $productImage->getSize(),
                        'product_id' => $product->id,
                    ]);
                }
            }

            $tagsIds = $data['tags'];
            $colorsIds = $data['colors'];

            unset($data['tags'], $data['colors'], $data['product_images']);

            $product->update($data);
            $product->tags()->sync($tagsIds);
            $product->colors()->sync($colorsIds);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public function delete($product)
    {
        DB::beginTransaction();
        try {
            $productImages = $product->productImages()->get();
            foreach ($productImages as $productImage){
                Storage::disk('public')->delete($productImage->file_path);
            }
            Storage::disk('public')->delete($product->preview_image);

            $product->productImages()->delete();
            $product->tags()->detach();
            $product->colors()->detach();
            $product->delete();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}

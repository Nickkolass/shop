<?php

namespace App\Services\API\Order;

use App\Components\Method;
use App\Models\Product;
use App\Models\ProductType;

class OrderProductService
{

    public function getProducts($orders, ?bool $show = false)
    {
        !$show ?: $orders = collect([0 => $orders]);

        $orders->map(function ($order) use ($show) {
            $productTypes = json_decode($order->productTypes, true);
            $pTs = ProductType::with('category:categories.id,categories.title')->when($show, function ($q) {
                $q->with(['optionValues.option:id,title', 'product' => function ($b) {
                    $b->select('id', 'saler_id', 'title')->with(['saler:id,name']);
                }]);
            })->select('id', 'product_id', 'preview_image')->find(array_column($productTypes, 'productType_id'));

            $show ? $this->forShow($productTypes, $pTs, $order->orderPerformers) : $this->forIndex($productTypes, $pTs);

            $order->productTypes = $productTypes;
        });
        return $orders;
    }


    private function forIndex(&$productTypes, $pTs)
    {
        foreach ($productTypes as &$productType) {
            $pT = $pTs->where('id', $productType['productType_id'])->first();
            $productType['preview_image'] = $pT->preview_image;
            $productType['category'] = $pT->category->title;
        }
    }


    private function forShow(&$productTypes, $pTs, $orderPerformers)
    {
       
        foreach ($productTypes as &$productType) {
            $prod = $productType;
            $orderPerformer = $orderPerformers->where('saler_id', $prod['saler_id'])->first();
            $productType = $pTs->where('id', $prod['productType_id'])->first();
            
            Method::valuesToKeys($productType, 'optionValues');
            
            $productType->amount = $prod['amount'];
            $productType->price = $prod['price'];
            $productType->status = $orderPerformer->status;
            $productType->orderPerformer_id = $orderPerformer->id;
        }

    }
}

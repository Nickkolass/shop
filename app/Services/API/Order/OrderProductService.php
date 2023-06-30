<?php

namespace App\Services\API\Order;

use App\Components\Method;
use App\Models\Order;
use App\Models\ProductType;
use Illuminate\Contracts\Pagination\Paginator;

class OrderProductService
{

    public function getProductsForShow(Order $order): Order
    {
        $productTypes = json_decode($order->productTypes, true);

        $pTs = ProductType::with(['category:categories.id,categories.title', 'optionValues.option:id,title', 'product' => function ($b) {
            $b->select('id', 'saler_id', 'title')->with(['saler:id,name']);
        }])->select('id', 'product_id', 'preview_image')->find(array_column($productTypes, 'productType_id'));

        foreach ($productTypes as &$productType) {
            $prod = $productType;
            $orderPerformer = $order->orderPerformers->where('saler_id', $prod['saler_id'])->first();
            $productType = $pTs->where('id', $prod['productType_id'])->first();

            Method::valuesToKeys($productType, 'optionValues');

            $productType->amount = $prod['amount'];
            $productType->price = $prod['price'];
            $productType->status = $orderPerformer->status;
            $productType->orderPerformer_id = $orderPerformer->id;
        }
        $order->productTypes = $productTypes;

        return $order;
    }


    public function getProductsForIndex($orders): ?Paginator
    {
        foreach ($orders as $order) {
            $ordersProductTypes[] = json_decode($order->productTypes, true);
        }

        $pTs = ProductType::with('category:categories.id,categories.title')->select('id', 'product_id', 'preview_image')
            ->find(array_column(array_merge(...$ordersProductTypes), 'productType_id'));

        foreach ($ordersProductTypes as $key => &$productTypes) {
            foreach ($productTypes as &$productType) {
                $pT = $pTs->where('id', $productType['productType_id'])->first();
                $productType['preview_image'] = $pT->preview_image;
                $productType['category'] = $pT->category->title;
            }
            $orders[$key]->productTypes = $productTypes;
        }
        return $orders;
    }
}

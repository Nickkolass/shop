<?php

namespace App\Services\API\Order;

use App\Components\Method;
use App\Models\Product;

class OrderProductService
{

    public function getProducts($orders, ?bool $show = false)
    {
        !$show ?: $orders = collect([0 => $orders]);

        $orders->map(function ($order) use ($show) {
            $products = json_decode($order->products, true);
            $prods = Product::select('id', 'category_id', 'preview_image')->with('category:id,title')->when($show, function ($q) {
                $q->with(['saler:id,name', 'optionValues.option:id,title'])->addSelect('title', 'saler_id');
            })->find(array_column($products, 'product_id'));

            $show ? $this->forShow($products, $prods, $order->orderPerformers) : $this->forIndex($products, $prods);

            $order->products = $products;
        });
        return $orders;
    }


    private function forIndex(&$products, $prods)
    {
        foreach ($products as &$product) {
            $prod = $prods->where('id', $product['product_id'])->first();
            $product['preview_image'] = $prod->preview_image;
            $product['category'] = $prod->category->title;
        }
    }


    private function forShow(&$products, $prods, $orderPerformers)
    {
        foreach ($products as &$product) {
            $prod = $product;
            $orderPerformer = $orderPerformers->where('saler_id', $prod['saler_id'])->first();
            $product = $prods->where('saler_id', $prod['saler_id'])->first();

            $product->setRelation('optionValues', $product->optionValues->whereIn('id', $prod['optionValues']));
            Method::valuesToKeys($product, 'optionValues');

            $product->amount = $prod['amount'];
            $product->price = $prod['price'];
            $product->dispatch_time = $orderPerformer->dispatch_time;
            $product->orderPerformer = [
                'id' => $orderPerformer->id,
                'status' => $orderPerformer->status,
            ];
        }
    }
}

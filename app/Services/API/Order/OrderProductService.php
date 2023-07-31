<?php

namespace App\Services\API\Order;

use App\Components\Method;
use App\Models\Order;
use App\Models\ProductType;
use Illuminate\Contracts\Pagination\Paginator;

class OrderProductService
{
    public function getProductsForIndex(Paginator &$orders): void
    {
        $productType_ids = $orders->pluck('productTypes.*.productType_id')->flatten();
        $preview_images = ProductType::query()
            ->whereIn('id', $productType_ids)
            ->pluck('preview_image', 'id');

        foreach ($orders as &$order) {
            foreach ($order->productTypes as $productType) {
                $productType['preview_image'] = $preview_images[$productType['productType_id']];
                $productTypes[] = $productType;
            }
            $order->productTypes = $productTypes;
            unset($productTypes);
        }
    }

    public function getProductsForShow(Order &$order): void
    {
        $pTs = collect($order->productTypes);
        $order->productTypes = ProductType::query()
            ->whereIn('id', $pTs->pluck('productType_id'))
            ->select('id', 'product_id', 'preview_image')
            ->with([
                'product:products.id,title',
                'saler:users.id,name',
                'optionValues.option:id,title',
            ])
            ->get()
            ->each(function ($productType) use ($pTs, $order) {
                $pT = $pTs->firstWhere('productType_id', $productType->id);
                $orderPerformer = $order->orderPerformers->firstWhere('saler_id', $pT['saler_id']);
                $productType->amount = $pT['amount'];
                $productType->price = $pT['price'];
                $productType->orderPerformer_id = $orderPerformer->id;
                $productType->status = $orderPerformer->status;
                Method::valuesToKeys($productType, 'optionValues');
            });
    }
}

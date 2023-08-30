<?php

namespace App\Services\Client\API\Order;

use App\Models\Order;
use App\Models\ProductType;
use App\Services\Methods\Maper;
use Illuminate\Contracts\Pagination\Paginator;

class OrderProductService
{
    public function getProductsForIndex(Paginator &$orders): void
    {
        $productType_ids = $orders->getCollection()->pluck('productTypes.*.productType_id')->flatten();
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
        $productTypesFromOrder = collect($order->productTypes);
        $order->productTypes = ProductType::query()
            ->whereIn('id', $productTypesFromOrder->pluck('productType_id'))
            ->select('id', 'product_id', 'preview_image')
            ->with([
                'product:products.id,title',
                'saler:users.id,name',
                'optionValues.option:id,title',
            ])
            ->get()
            ->each(function (ProductType $productType) use ($productTypesFromOrder, $order) {
                $productTypeFromOrder = $productTypesFromOrder->firstWhere('productType_id', $productType->id);
                $orderPerformer = $order->orderPerformers->firstWhere('saler_id', $productTypeFromOrder['saler_id']);
                $productType->amount = $productTypeFromOrder['amount'];
                $productType->price = $productTypeFromOrder['price'];
                $productType->orderPerformer_id = $orderPerformer->id;
                $productType->status = $orderPerformer->status;
                Maper::valuesToKeys($productType, 'optionValues');
            });
    }
}

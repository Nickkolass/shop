<?php

namespace App\Services\Client\API\Order;

use App\Models\Order;
use App\Models\ProductType;
use App\Services\Methods\Maper;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

class OrderProductService
{
    public function getProductsForIndex(Paginator &$orders): void
    {
        /** @var Collection $orders */
        $productType_ids = $orders->pluck('productTypes.*.productType_id')->flatten();
        $preview_images = ProductType::query()
            ->whereIn('id', $productType_ids)
            ->pluck('preview_image', 'id');

        $orders->map(function (Order $order) use ($preview_images) {
            $productTypes = [];
            foreach ($order->productTypes as $productType) {
                $productType['preview_image'] = $preview_images[$productType['productType_id']];
                $productTypes[] = $productType;
            }
            $order->productTypes = $productTypes;
            unset($productTypes);
        });
    }

    public function getProductsForShow(Order &$order): void
    {
        $productTypesFromOrder = collect($order->productTypes);
        $order->productTypes = ProductType::query()
            ->whereIn('id', $productTypesFromOrder->pluck('productType_id'))
            ->with([
                'product:products.id,title',
                'saler:users.id,name',
                'optionValues.option:id,title',
            ])
            ->select('id', 'product_id', 'preview_image')
            ->get()
            ->each(function (ProductType $productType) use ($productTypesFromOrder, $order) {
                $productTypeFromOrder = $productTypesFromOrder->firstWhere('productType_id', $productType->id);
                $orderPerformer = $order->orderPerformers->firstWhere('saler_id', $productTypeFromOrder['saler_id']);
                $productType->setAttribute('amount', $productTypeFromOrder['amount']);
                $productType->setAttribute('price', $productTypeFromOrder['price']);
                $productType->setAttribute('orderPerformer_id', $orderPerformer->id);
                $productType->setAttribute('status', $orderPerformer->status);
                Maper::valuesToKeys($productType, 'optionValues');
            });
    }
}

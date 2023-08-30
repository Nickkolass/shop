<?php

namespace App\Services\Admin\OrderPerformer;

use App\Models\OrderPerformer;
use App\Models\ProductType;
use App\Services\Methods\Maper;
use Illuminate\Contracts\Pagination\Paginator;

class OrderPerformerProductService
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

    public function getProductsForShow(OrderPerformer &$order): void
    {
        $productTypesFromOrder = collect($order->productTypes);
        $order->productTypes = ProductType::query()
            ->whereIn('id', $productTypesFromOrder->pluck('productType_id'))
            ->select('id', 'product_id', 'preview_image')
            ->with([
                'category:categories.id,title_rus',
                'optionValues.option:id,title',
                'product:id,title'
            ])
            ->get()
            ->each(function (ProductType $productType) use ($productTypesFromOrder) {
                $productTypeFromOrder = $productTypesFromOrder->where('productType_id', $productType->id)->first();
                $productType->amount = $productTypeFromOrder['amount'];
                $productType->price = $productTypeFromOrder['price'];
                Maper::valuesToKeys($productType, 'optionValues');
            });
    }
}

<?php

namespace App\Services\Admin\OrderPerformer;

use App\Components\Method;
use App\Models\OrderPerformer;
use App\Models\ProductType;
use Illuminate\Contracts\Pagination\Paginator;


class OrderPerformerProductService
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

    public function getProductsForShow(OrderPerformer &$order): void
    {
        $pTs = collect($order->productTypes);
        $order->productTypes = ProductType::query()
            ->whereIn('id', $pTs->pluck('productType_id'))
            ->select('id', 'product_id', 'preview_image')
            ->with([
                'category:categories.id,title_rus',
                'optionValues.option:id,title',
                'product:id,title'
            ])
            ->get()
            ->each(function ($productType) use ($pTs) {
                $pT = $pTs->where('productType_id', $productType->id)->first();
                $productType->amount = $pT['amount'];
                $productType->price = $pT['price'];
                Method::valuesToKeys($productType, 'optionValues');
            });
    }

}

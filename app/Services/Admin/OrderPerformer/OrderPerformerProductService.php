<?php

namespace App\Services\Admin\OrderPerformer;

use App\Models\OrderPerformer;
use App\Models\ProductType;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

class OrderPerformerProductService
{
    public function getProductsForIndex(Paginator $orders): void
    {
        /** @var Collection $orders */
        $productType_ids = $orders->pluck('productTypes.*.productType_id')->flatten();
        $preview_images = ProductType::query()
            ->whereIn('id', $productType_ids)
            ->pluck('preview_image', 'id');

        $orders->each(function (OrderPerformer $order) use ($preview_images) {
            $productTypes = $order->productTypes;
            foreach ($productTypes as &$productType) {
                $productType['preview_image'] = $preview_images[$productType['productType_id']];
            }
            $order->productTypes = $productTypes;
        });
    }

    public function getProductsForShow(OrderPerformer $order): void
    {
        $productTypesFromOrder = collect($order->productTypes);
        $order->productTypes = ProductType::query()
            ->whereIn('id', $productTypesFromOrder->pluck('productType_id'))
            ->select('id', 'product_id', 'preview_image')
            ->with([
                'product:id,title',
                'category:categories.id,title_rus',
                'optionValues' => function (Builder $q) {
                    /** @phpstan-ignore-next-line */
                    $q->select('value')->selectParentTitle();
                }])
            ->get()
            ->each(function (ProductType $productType) use ($productTypesFromOrder) {
                $productTypeFromOrder = $productTypesFromOrder->firstWhere('productType_id', $productType->id);
                $productType->setAttribute('amount', $productTypeFromOrder['amount']);
                $productType->price = $productTypeFromOrder['price'];
                $productType->setRelation('optionValues', $productType->optionValues->pluck('value', 'option_title'));
            });
    }
}

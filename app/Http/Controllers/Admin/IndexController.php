<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderPerformer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class IndexController extends Controller
{

    public function __invoke(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $data['orders'] = $user->orderPerformers()
            ->toBase()
            ->limit(3)
            ->where('status', OrderPerformer::STATUS_WAIT_DELIVERY)
            ->select('id', 'total_price')
            ->latest()
            ->get();

        $query = $user->orderPerformers()
            ->where('status', OrderPerformer::STATUS_PAYOUT)
            ->whereMonth('created_at', (string)now()->previous('month'));
        $data['month_payout'] = [
            'payout' => $query
                ->selectRaw('COUNT(*) as count, SUM(total_price) as revenue')
                ->toBase()
                ->first(),
            'orders' => $query
                ->select('id', 'total_price')
                ->limit(3)
                ->toBase()
                ->get(),
        ];

        $data['productTypes_ordered'] = $user->orderPerformers()
            ->pluck('productTypes')
            ->flatten(1)
            ->groupBy('productType_id')
            /** @phpstan-ignore-next-line */
            ->transform(fn(Collection $productType) => [
                'amount' => $productType->sum('amount'),
                'price' => $productType->sum('price')
            ])
            ->sortByDesc('amount')
            ->take(3)/** @phpstan-ignore-next-line */
            ->transform(function ($data, $productType_id) {
                $product = Product::query()
                    ->whereHas('productTypes', fn(Builder $q) => $q->limit(1)->where('productTypes.id', $productType_id))
                    ->select('id', 'title')
                    ->first();
                return [
                    'productType_id' => $product->id,
                    'amount' => $data['amount'],
                    'price' => $data['price'],
                    'title' => $product->title,
                ];
            });

        $query = $user->productTypes()->where('is_published', 0);
        $data['unpublished'] = [
            'productTypes' => $query
                ->toBase()
                ->limit(3)
                ->select('productTypes.id', 'product_id', 'title')
                ->groupBy('product_id')
                ->get(),
            'count' => $query->count()
        ];

        $data['products_rating'] = $user->products()
            ->toBase()
            ->limit(3)
            ->select('products.id', 'title', 'count_rating', 'rating')
            ->orderByDesc('rating')
            ->get();

        $data['productTypes_liked'] = $user->productTypes()
            ->toBase()
            ->limit(3)
            ->select('productTypes.id', 'product_id', 'title', 'count_likes')
            ->orderByDesc('count_likes')
            ->get();

        return view('admin.index', compact('data'));
    }
}

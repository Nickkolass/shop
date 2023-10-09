<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class IndexController extends Controller
{

    public function __invoke(): View
    {
        $user = auth()->user();
        /** @var User $user */
        $data['orders'] = $user->orderPerformers()
            ->toBase()
            ->limit(3)
            ->where('status', 'В работе')
            ->select('id', 'total_price')
            ->orderByDesc('total_price')
            ->get();

        $query = $user->orderPerformers()->where('status', 'like', 'Получен' . '%');
        $data['revenue'] = [
            'month' => now()->monthName,
            'orders' => $query->toBase()->limit(3)->select('id', 'total_price')->get(),
            'count_orders' => $query->count(),
            'revenue' => $query->whereMonth('created_at', '=', now()->month)->sum('total_price')
        ];

        $query = $user->productTypes()->where('is_published', 0);
        $data['product_published_count'] = $query->count();
        $data['product_published'] = $query
            ->toBase()
            ->limit(3)
            ->select('productTypes.id', 'product_id', 'title')
            ->groupBy('product_id')
            ->get();

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

        $data['productTypes_ordered'] = $user->orderPerformers()
            ->pluck('productTypes')
            ->flatten(1)
            ->groupBy('productType_id')/** @phpstan-ignore-next-line */
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

        return view('admin.index', compact('data'));
    }
}

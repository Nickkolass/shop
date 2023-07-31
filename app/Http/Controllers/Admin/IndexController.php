<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class IndexController extends Controller
{


    public function __invoke(): View
    {
        $user = auth()->user();

        $data['orders'] = $user->orderPerformers()
            ->limit(3)
            ->where('status', 'В работе')
            ->select('id', 'total_price')
            ->orderBy('total_price', 'DESC')
            ->get();

        $query = $user->orderPerformers()->where('status', 'like', 'Получен' . '%');
        $data['revenue'] = [
            'month' => now()->monthName,
            'orders' => $query->limit(3)->select('id', 'total_price')->get(),
            'count_orders' => $query->count(),
            'revenue' => $query->whereMonth('created_at', '=', now()->month)->sum('total_price')
        ];

        $query = $user->productTypes()->where('is_published', 0);
        $data['product_published_count'] = $query->count();
        $data['product_published'] = $query
            ->limit(3)
            ->select('productTypes.id', 'product_id', 'title')
            ->groupBy('product_id')
            ->get();

        $data['products_rating'] = $user->products()
            ->limit(3)
            ->select('products.id', 'product_id', 'title')
            ->leftJoin('rating_and_comments', 'products.id', '=', 'rating_and_comments.product_id')
            ->selectRaw('COUNT(rating) AS rating_count, AVG(rating) AS rating')
            ->groupBy('id')
            ->orderBy('rating', 'DESC')
            ->get();

        $data['productTypes_liked'] = $user->productTypes()
            ->limit(3)
            ->select('productTypes.id', 'product_id', 'title')
            ->leftJoin('users', 'productTypes.id', '=', 'users.id')
            ->selectRaw('COUNT(users.id) AS liked_count')
            ->groupBy('id')
            ->orderBy('liked_count', 'DESC')
            ->get();

        $data['productTypes_ordered'] = $user->orderPerformers()
            ->pluck('productTypes')
            ->flatten(1)
            ->groupBy('productType_id')
            ->map(fn($pT) => ['amount' => $pT->sum('amount'), 'price' =>  $pT->sum('price')])
            ->sortDesc()
            ->take(3)
            ->map(function ($data, $pT_id) {
                $p = Product::whereHas('productTypes', fn($q) => $q->limit(1)->where('productTypes.id', $pT_id))->select('id', 'title')->first();
                return [
                    'productType_id' => $p->id,
                    'amount' => $data['amount'],
                    'price' => $data['price'],
                    'title' => $p->title,
                ];
            });

        return view('admin.index', compact('data'));
    }
}

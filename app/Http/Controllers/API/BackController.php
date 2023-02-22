<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Filters\ProductFilter;
use App\Http\Requests\API\Order\StoreRequest;
use App\Http\Requests\API\Product\FilterRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\ProductShowResource;
use App\Http\Resources\ProductsResource;
use App\Models\Category;
use App\Models\Color;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BackController extends Controller
{
    public function show($category, Product $product)
    {
        $data['product'] = $product;
        $data['category'] = $category;
        return new ProductShowResource($data);
    }

    public function cart()
    {
        $keys = array_keys($_REQUEST);
        $data = Product::find($keys)->sortBy(function ($i, $k) use ($keys) {
            return array_search($i->id, $keys);
        });
        return CartResource::collection($data);
    }

    public function products(Category $category, FilterRequest $request)
    {
        $data['request'] = $request->validated();
        // return $data;
        if($data['request']['is_published'] == 1){
            $products_ids =  $category->products()->where('is_published', '=', 1)->pluck('id');
        } else {
            $products_ids =  $category->products()->pluck('id');
        }
        $data['category'] = $category;
        $data['salers'] = User::whereHas('products', function ($b) use ($products_ids) {
            $b->whereIn('id', $products_ids);
        })->select('id', 'name')->get();
        $data['colors'] = Color::whereHas('products', function ($b) use ($products_ids) {
            $b->whereIn('product_id', $products_ids);
        })->select('id', 'title')->get();
        $data['tags'] = Tag::whereHas('products', function ($b) use ($products_ids) {
            $b->whereIn('product_id', $products_ids);
        })->select('id', 'title')->get();
        $data['prices'] =  [
            'minPrice' => $category->products()->min('price'),
            'maxPrice' => $category->products()->max('price'),
        ];
        $filter = app()->make(ProductFilter::class, ['queryParams' => array_filter($data['request'])]);
        unset($data['request']['category']);
        $query = Product::with(['tags:id,title', 'colors:id,title', 'saler:id,name', 'productImages:product_id,file_path'])->filter($filter);
        $data['request']['is_published'] == 1 ? $query = $query->where('is_published', '=', 1) : $query = $query->orderBy('is_published', 'desc');
        $data['request']['orderBy'] == 'latest' ? $query = $query->latest() : $query = $query->orderBy('price', $data['request']['orderBy']);
        $data['products'] = $query->paginate($data['request']['perPage'], ['*'], 'page', $data['request']['page'])->withPath('');
        return new ProductsResource($data);
    }

    public function ordering(StoreRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            foreach ($data['cart'] as $k => $v) {
                $product = Product::select('count', 'saler_id')->find($k);
                if (empty($orders)) {
                    $orders = [$product->saler_id => [$k => $v]];
                } else {
                    $orders[$product->saler_id][$k] = $v;
                }
                $upd['count'] = $product->count - $v;
                if($product->count - $v = 0) {
                    $upd ['is_published'] = 0;
                }
                $product->update($upd);
            }
            foreach ($orders as $saler_id => $order) {
                OrderPerformer::create([
                    'saler_id' => $saler_id,
                    'user_id' => $data['user'],
                    'products' => json_encode($order),
                    'dispatch_time' => date("d-m-y", mktime(0, 0, 0, date("m"), date("d") + 7, date("y"))),
                    'delivery' => $data['delivery'],
                    'total_price' => $data['total_price'],
                ]);
            }

            Order::create([
                'user_id' => $data['user'],
                'products' => json_encode($data['cart']),
                'delivery' => $data['delivery'],
                'total_price' => $data['total_price'],
                'payment_status' => $data['payment_status'],
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }

    public function orders()
    {
        $user = User::find(request('user_id'));
        $data['orders'] = $user->orders()->latest('created_at')->get()->toArray();
        foreach ($data['orders'] as &$order) {
            $order['products'] = json_decode($order['products'], true);
            foreach ($order['products'] as $k => $v) {
                $order['products'][$k] = Product::select('id', 'title', 'price', 'preview_image', 'category_id')->find($k)->toArray();
                $order['products'][$k]['amount'] = $v;
                $order['products'][$k]['category'] = Category::find($order['products'][$k]['category_id'])->pluck('title')['0'];
            }
            $order['products'] = array_values($order['products']);
            $order['created_at'] = explode('T', $order['created_at'])['0'];
        }
        return $data;
    }
}

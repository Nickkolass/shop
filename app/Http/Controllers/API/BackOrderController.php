<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Order\StoreRequest;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BackOrderController extends Controller
{
    
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (User::find(request('user_id'))->role == 'admin') {
            $data['orders'] = Order::latest('created_at');
        } else {
            $data['orders'] = User::find(request('user_id'))->orders()->latest('created_at');
        }
        $data['orders'] = $data['orders']->withTrashed()->paginate(10, ['*'], 'page', request('page'))->withPath('')->toArray();
        array_pop($data['orders']['links']);
        array_shift($data['orders']['links']);
        foreach ($data['orders']['data'] as &$order) {
            $order['products'] = json_decode($order['products'], true);
            foreach ($order['products'] as $product_id => $amount) {
                $order['products'][$product_id] = Product::select('id', 'title', 'price', 'preview_image', 'category_id', 'saler_id')->find($product_id)->toArray();
                $order['products'][$product_id]['amount'] = $amount;
                $order['products'][$product_id]['category'] = Category::find($order['products'][$product_id]['category_id'])->pluck('title')['0'];
                $order['products'][$product_id]['deleted_at'] = OrderPerformer::where('order_id', $order['id'])->where('saler_id', $order['products'][$product_id]['saler_id'])->pluck('deleted_at');
            }
            $order['products'] = array_values($order['products']);
            $order['created_at'] = explode('T', $order['created_at'])['0'];
            $order['deleted_at'] = explode('T', $order['deleted_at'])['0'];
            $order['dispatch_time'] = OrderPerformer::where('order_id', $order['id'])->max('dispatch_time');
        }
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $user = User::select('surname', 'name', 'patronymic', 'address')->find($data['user_id']);
        $data['delivery'] .= '. Получатель: ' . $user->surname . ' ' . $user->name . ' ' . $user->patronymic . '. Адрес: ' . $user->address;
        $orders = $prices = [];
        foreach ($data['cart'] as $product_id => $amount) {
            $product = Product::select('id', 'count', 'saler_id', 'price')->find($product_id);
            $orders[$product->saler_id][$product_id] = $amount;
            $prices[$product_id] = $product->price * $amount;
            $upd['count'] = $product->count - $amount;
            $upd['count'] == 0 ? $upd['is_published'] = 0 : '';
            $product->update($upd);
        }
        DB::beginTransaction();
        try {
            $ord = Order::create([
                'user_id' => $data['user_id'],
                'products' => json_encode($data['cart']),
                'delivery' => $data['delivery'],
                'total_price' => $data['total_price'],
                'payment_status' => $data['payment_status'],
                'status' => '',
            ]);

            foreach ($orders as $saler_id => $order) {
                $price = 0;
                foreach ($order as $product_id => $amount) {
                    $price += $prices[$product_id];
                }

                OrderPerformer::create([
                    'saler_id' => $saler_id,
                    'user_id' => $data['user_id'],
                    'order_id' => $ord->id,
                    'status' => '',
                    'products' => json_encode($order),
                    'dispatch_time' => now()->addDays(25),
                    'delivery' => $data['delivery'],
                    'total_price' => $price,
                ]);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $data['order'] = $order->toArray();
        $data['order']['deleted_at'] = explode('T', $data['order']['deleted_at'])['0'];
        $data['order']['created_at'] = explode('T', $data['order']['created_at'])['0'];
        $data['order']['dispatch_time'] = OrderPerformer::where('order_id', $data['order']['id'])->max('dispatch_time');
        $data['products'] = json_decode($data['order']['products'], true);
        foreach ($data['products'] as $product_id => $amount) {
            $data['products'][$product_id] = Product::with(['category:id,title', 'saler:id,name'])->select('id', 'title', 'price', 'category_id', 'preview_image', 'saler_id')->find($product_id);
            $data['products'][$product_id]['amount'] = $amount;
            $orderPerformer = OrderPerformer::where('order_id', $data['order']['id'])->where('saler_id', $data['products'][$product_id]->saler_id)->select('id', 'status', 'deleted_at')->withTrashed()->first();
            $data['products'][$product_id]['orderPerformer_id'] = $orderPerformer->id;
            $data['products'][$product_id]['status'] = $orderPerformer->status;
            $data['products'][$product_id]['deleted_at'] = explode('T', $orderPerformer->deleted_at)['0'];
        }
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Order $order)
    {
        $order->update(['status' => now()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        DB::beginTransaction();
        try {
            $order->orderPerformers()->delete();
            $order->delete();
            //возврат денег
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}

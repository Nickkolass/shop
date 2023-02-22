<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderPerformer;
use App\Models\Product;
use App\Models\User;

class OrderController extends Controller
{
    public function __invoke()
    {
        if (auth()->user()->role == 'admin') {
            $orders = OrderPerformer::latest('created_at')->get()->toArray();
        } else {
            $orders = auth()->user()->orderPerformers()->latest('created_at')->get()->toArray();
        }
        foreach ($orders as &$order) {
            $order['products'] = json_decode($order['products'], true);
            foreach ($order['products'] as $k => $v) {
                $order['products'][$k] = Product::select('id', 'preview_image')->find($k)->toArray();
                $order['products'][$k]['amount'] = $v;
            }
            $order['products'] = array_values($order['products']);
            $order['created_at'] = explode('T', $order['created_at'])['0'];
            $order['saler'] = User::select('id', 'name')->find($order['saler_id'])->toArray();
            $order['user'] = User::select('id', 'name')->find($order['user_id'])->toArray();
        }
        return view('admin.order.index_order', compact('orders'));
    }
}

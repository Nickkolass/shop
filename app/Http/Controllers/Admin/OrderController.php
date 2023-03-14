<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->role == 'admin') {
            $orders = OrderPerformer::latest('created_at');
        } else {
            $orders = auth()->user()->orderPerformers()->latest('created_at');
        }
        $orders = $orders->withTrashed()->paginate(5);
        foreach ($orders as &$order) {
            $products = json_decode($order->products, true);
            foreach ($products as $product_id => $amount) {
                $products[$product_id] = Product::select('id', 'preview_image')->find($product_id);
                $products[$product_id]->amount = $amount;
            }
            $order->products = array_values($products);
            $order->end = Order::withTrashed()->find($order->order_id)->status;
            if (auth()->user()->role == 'admin') {
                $order->saler = User::select('id', 'name')->find($order->saler_id);
                $order->user = User::select('id', 'name')->find($order->user_id);
            }
        }
        return view('admin.order.index_order', compact('orders'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderPerformer  $order
     * @return \Illuminate\Http\Response
     */
    public function show(OrderPerformer $order)
    {
        $this->authorize('view', $order);
        $order = $order->toArray();
        $order['created_at'] = explode('T', $order['created_at'])['0'];
        $order['deleted_at'] = explode('T', $order['deleted_at'])['0'];
        $order['end'] = Order::withTrashed()->find($order['order_id'])->status;
        $products = json_decode($order['products'], true);
        foreach ($products as $product_id => $amount) {
            $products[$product_id] = Product::with(['category:id,title,title_rus', 'saler:id,name'])->select('id', 'title', 'preview_image', 'price', 'category_id', 'saler_id')->find($product_id)->toArray();
            $products[$product_id]['amount'] = $amount;
        }
        return view('admin.order.show_order', compact('order', 'products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\OrderPerformer  $order
     * @return \Illuminate\Http\Response
     */
    public function update(OrderPerformer $order)
    {
        $this->authorize('update', $order);
        $order->update(['status' => now()]);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderPerformer  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderPerformer $order)
    {
        $this->authorize('delete', $order);
        DB::beginTransaction();
        try {
            $order->delete();
            $deleted_at = $order->order()->first()->orderPerformers()->withTrashed()->pluck('deleted_at')->toArray();
            !array_search(null, $deleted_at) ? $order->order()->delete() : '';
            //возврат денег
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
        return back();
    }
}

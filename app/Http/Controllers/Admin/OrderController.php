<?php

namespace App\Http\Controllers\Admin;

use App\Components\Method;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\Product;
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

        $orders = auth()->user()->role == 'admin' ? OrderPerformer::with('user:id,name') : auth()->user()->orderPerformers();
        $orders = $orders->latest('created_at')->withTrashed()->with('saler:id,name')->simplePaginate(5);
        foreach ($orders as &$order) {
            $order->products = json_decode($order->products);
            foreach ($order->products as $product) {
                $prod = Product::select('preview_image', 'title')->find($product->product_id);
                $product->preview_image = $prod->preview_image;
                $product->title = $prod->title;
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

        $products = json_decode($order->products, true);
        foreach ($products as &$product) {
            $prod = $product;
            $product = Product::select('id', 'title', 'preview_image', 'price', 'category_id', 'saler_id')->with([
                'category:id,title,title_rus', 'saler:id,name', 'optionValues' => function ($q) use ($prod) {
                    $q->with('option:id,title')->whereIn('optionValues.id', $prod['optionValues'])
                    ->select('optionValues.id', 'option_id', 'value');
                }
            ])->find($prod['product_id']);

            $product->amount = $prod['amount'];
            $product->price = $prod['price'];
            Method::valuesToKeys($product, 'optionValues');
        }
        $order->products = $products;
        return view('admin.order.show_order', compact('order'));
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
        $order->update(['status' => 'Отправлен ' . now()]);
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
            $order->update(['status' => 'Отменен ' . now()]);
            $order->delete();
            //возврат денег
            Order::where('id', $order->order_id)->doesntHave('orderPerformers')->update(['status' => 'Отменен ' . now()]);
            Order::where('id', $order->order_id)->doesntHave('orderPerformers')->delete();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
        return back();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Components\Method;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\Product;
use App\Models\ProductType;
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

        $orders = session('user_role') == 'admin' ? OrderPerformer::with('user:id,name') : auth()->user()->orderPerformers();
        $orders = $orders->latest('created_at')->withTrashed()->with('saler:id,name')->simplePaginate(5);
        if ($orders->count() != 0) {
            foreach ($orders as $order) {
                $ordersProductTypes[] = json_decode($order->productTypes);
            }

            $preview_images = ProductType::whereIn('id', array_column(array_merge(...$ordersProductTypes), 'productType_id'))
                ->select('id', 'product_id', 'preview_image')->pluck('preview_image', 'id');

            foreach ($ordersProductTypes as $key => &$productTypes) {
                foreach ($productTypes as &$productType) {
                    $productType->preview_image = $preview_images[$productType->productType_id];
                }
                $orders[$key]->productTypes = $productTypes;
            }
        }
        return view('admin.order.index', compact('orders'));
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

        $order->load('saler:users.id,name');
        $pTs = collect(json_decode($order->productTypes));
        $order->productTypes = ProductType::whereIn('id', $pTs->pluck('productType_id'))->select('id', 'product_id', 'preview_image')
            ->with(['category:categories.id,title_rus', 'optionValues.option:id,title', 'product:id,title'])->get();

        $order->productTypes->map(function ($productType) use ($pTs) {
            $pT = $pTs->where('productType_id', $productType->id)->first();
            $productType->amount = $pT->amount;
            $productType->price = $pT->price;
            Method::valuesToKeys($productType, 'optionValues');
        });

        return view('admin.order.show', compact('order'));
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

        $order->order()->when(function ($q) use ($order) {
            return OrderPerformer::where('order_id', $order->order_id)->where('status', 'В работе')->count() == 0;
        }, function ($q) {
            $q->update(['status' => 'Отправлен']);
        });
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

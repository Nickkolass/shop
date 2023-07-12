<?php

namespace App\Http\Controllers\Admin;

use App\Components\Method;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\ProductType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $this->authorize('viewAny', OrderPerformer::class);

        $user = session('user');
        $orders = $user['role'] == 'admin' ? OrderPerformer::with('user:id,name') : OrderPerformer::whereHas('saler', function($q) use($user) {
            $q->where('id', $user['id']);
        });;
        $orders = $orders->latest('created_at')->withTrashed()->with('saler:id,name')->simplePaginate(5);
        if ($orders->count() != 0) {
            foreach ($orders as $order) $ordersProductTypes[] = json_decode($order->productTypes);

            $preview_images = ProductType::whereIn('id', array_column(array_merge(...$ordersProductTypes), 'productType_id'))
                ->select('id', 'product_id', 'preview_image')->pluck('preview_image', 'id');

            foreach ($ordersProductTypes as $key => &$productTypes) {
                foreach ($productTypes as &$productType) $productType->preview_image = $preview_images[$productType->productType_id];
                $orders[$key]->productTypes = $productTypes;
            }
        }
        return view('admin.order.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     *
     * @param OrderPerformer $order
     * @return View
     */
    public function show(OrderPerformer $order): View
    {
        $this->authorize('view', $order);

        $order->load('saler:users.id,name');
        $pTs = collect(json_decode($order->productTypes));
        $order->productTypes = ProductType::whereIn('id', $pTs->pluck('productType_id'))->select('id', 'product_id', 'preview_image')
            ->with(['category:categories.id,title_rus', 'optionValues.option:id,title', 'product:id,title'])->get()
            ->each(function ($productType) use ($pTs) {
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
     * @param OrderPerformer $order
     * @return RedirectResponse
     */
    public function update(OrderPerformer $order): RedirectResponse
    {
        $this->authorize('update', $order);
        $order->update(['status' => 'Отправлен ' . now()]);

        $order->order()->whereHas('orderPerformers', function ($q) use ($order) {
            $q->where('order_id', $order->order_id)->where('status', '!=', 'В работе');
        })->update(['status' => 'Отправлен']);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param OrderPerformer $order
     * @return RedirectResponse
     */
    public function destroy(OrderPerformer $order): RedirectResponse|string
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

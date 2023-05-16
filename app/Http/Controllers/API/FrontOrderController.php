<?php

namespace App\Http\Controllers\API;

use App\Components\ImportDataClient;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Order\StoreFrontRequest;
use App\Models\Order;
use App\Models\ProductType;

class FrontOrderController extends Controller
{
    private $import;

    public function __construct(ImportDataClient $import)
    {
        $this->import = $import;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $data['page'] = request('page') ?? 1;
        $data['user_id'] = auth()->id();
        
        $orders = $this->import->client->request('POST', 'api/orders', ['query' => $data])->getBody()->getContents();
        $orders = json_decode($orders, true);

        return view('api.order.index_order', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        url()->previous() == 'http://127.0.0.1:8876/cart' ?: abort(404);
        $totalPrice = request('totalPrice');
        return view('api.order.create_order', compact('totalPrice'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFrontRequest $request)
    {
        $data = $request->validated();
        //платежная система
        $this->import->client->request('POST', 'api/orders/store', ['query' => $data])->getBody()->getContents();

        session()->forget(['cart', 'filter', 'paginate']);

        return redirect()->route('api.orders_api');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($order_id)
    {
        $this->authorize('view', Order::withTrashed()->find($order_id));
        $order = $this->import->client->request('POST', 'api/orders/' . $order_id)->getBody()->getContents();
        $order = json_decode($order, true);
        return view('api.order.show_order', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($order_id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($order_id)
    {
        $this->authorize('update', Order::find($order_id));
        $this->import->client->request('PATCH', 'api/orders/' . $order_id);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($order_id)
    {
        $this->authorize('delete', Order::find($order_id));
        $this->import->client->request('DELETE', 'api/orders/' . $order_id)->getBody()->getContents();
        return back();
    }
}

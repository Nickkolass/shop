<?php

namespace App\Http\Controllers\API;

use App\Components\ImportDataClient;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

class FrontOrderController extends Controller
{
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        request()->has('page') ? $data['page'] = request('page') : $data['page'] = 1;
        $data['user_id'] = auth()->id();
        $import = new ImportDataClient();
        $data = $import->client->request('POST', 'api/orders', ['query' => $data])->getBody()->getContents();
        $data = json_decode($data, true);
        return view('api.order.index_order', compact('data'));    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        url()->previous() != 'http://127.0.0.1:8876/cart' ? abort(404) : '';
        $data['total_price'] = request('total_price');
        return view('api.order.create_order', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $data = request()->except('_token');
        $data['user_id'] = auth()->id();
        $data['cart'] = session('cart');
        //платежная система
        $data['payment_status'] = true;
        // dd($data);
        $import = new ImportDataClient();
        $import->client->request('POST', 'api/orders/create', ['query' => $data])->getBody()->getContents();
        session()->forget(['cart', 'filter']);
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
        $import = new ImportDataClient();
        $data = $import->client->request('POST', 'api/orders/' . $order_id, ['query' => $order_id])->getBody()->getContents();
        $data = json_decode($data, true);
        // dd($data);
        return view('api.order.show_order', compact('data'));
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
        $import = new ImportDataClient();
        $import->client->request('PATCH', 'api/orders/' . $order_id, ['query' => $order_id]);
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
        $import = new ImportDataClient();
        $import->client->request('DELETE', 'api/orders/' . $order_id, ['query' => $order_id])->getBody()->getContents();
        return back();
    }
}

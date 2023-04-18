<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Order\StoreRequest;
use App\Http\Resources\Order\OrdersResource;
use App\Http\Resources\Order\ShowOrderResource;
use App\Models\Order;
use App\Services\API\Order\OrderDBService;
use App\Services\API\Order\OrderService;

class BackOrderController extends Controller
{
    private $service;
    private $DBservice;

    public function __construct(OrderService $service, OrderDBService $DBservice)
    {
        $this->service = $service;
        $this->DBservice = $DBservice;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = $this->service->index(request('user_id'));
        return OrdersResource::make($orders)->resolve();
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
        $this->DBservice->store($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $this->service->show($order);
        return ShowOrderResource::make($order)->resolve();
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
        $this->DBservice->update($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $this->DBservice->delete($order);
    }
}

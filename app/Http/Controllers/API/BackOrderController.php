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
    private OrderService $service;
    private OrderDBService $DBservice;

    public function __construct(OrderService $service, OrderDBService $DBservice)
    {
        $this->authorizeResource(Order::class, 'order');
        $this->service = $service;
        $this->DBservice = $DBservice;
    }

    /**
     * Display a listing of the resource.
     *
     * @return ?array
     */
    public function index(): ?array
    {
        $orders = $this->service->index(request('page'));
        return isset($orders) ? OrdersResource::make($orders)->resolve() : null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return void
     */
    public function store(StoreRequest $request): void
    {
        $data = $request->validated();
        $this->DBservice->store($data);
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @return array
     */
    public function show(Order $order): array
    {
        $order = $this->service->show($order);
        return ShowOrderResource::make($order)->resolve();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Order $order
     * @return void
     */
    public function update(Order $order): void
    {
        $this->DBservice->update($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Order $order
     * @return void
     */
    public function destroy(Order $order): void
    {
        $this->DBservice->delete($order);
    }
}

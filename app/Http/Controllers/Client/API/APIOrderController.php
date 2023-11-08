<?php

namespace App\Http\Controllers\Client\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Order\StoreRequest;
use App\Http\Resources\Order\OrdersCollection;
use App\Http\Resources\Order\ShowOrderResource;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Services\Admin\OrderPerformer\OrderPerformerService;
use App\Services\Client\API\Order\OrderDBService;
use App\Services\Client\API\Order\OrderService;

class APIOrderController extends Controller
{

    public function __construct(private readonly OrderService $service, private readonly OrderDBService $DBservice)
    {
        $this->authorizeResource(Order::class, 'order');
    }

    /**
     * Display a listing of the resource.
     *
     * @return null|array<mixed>
     */
    public function index(): ?array
    {
        $orders = $this->service->index(request('page'));
        return isset($orders) ? OrdersCollection::make($orders)->resolve() : null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return string $payment_url
     */
    public function store(StoreRequest $request): string
    {
        $data = $request->validated();
        return $this->DBservice->store($data);
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @return array<mixed>
     */
    public function show(Order $order): array
    {
        $this->service->show($order);
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
        $this->DBservice->delete($order, request()->input('due_to_payment', false));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param OrderPerformer $orderPerformer
     * @return void
     */
    public function destroyOrderPerformer(OrderPerformer $orderPerformer): void
    {
        $this->authorize('delete', [$orderPerformer, true]);
        app(OrderPerformerService::class)->delete($orderPerformer, true);
    }
}

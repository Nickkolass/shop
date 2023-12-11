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
use Illuminate\Http\RedirectResponse;

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
     * @return RedirectResponse
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->DBservice->store($data);
        return redirect()
            ->with(['price' => $data['order']->total_price, 'order_id' => $data['order']->id])
            ->route('back.api.orders.pay', $data['order']->id);
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
        $this->DBservice->delete($order, request()->input('due_to_pay', false));
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

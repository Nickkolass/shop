<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderPerformer;
use App\Services\Admin\OrderPerformer\OrderPerformerService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class OrderPerformerController extends Controller
{

    public OrderPerformerService $service;

    public function __construct(OrderPerformerService $service)
    {
        $this->authorizeResource(OrderPerformer::class, 'order');
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $orders = $this->service->index();
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
        $this->service->show($order);
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
        $this->service->update($order);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param OrderPerformer $order
     * @return RedirectResponse
     */
    public function destroy(OrderPerformer $order): RedirectResponse
    {
        $this->service->delete($order);
        return back();
    }
}

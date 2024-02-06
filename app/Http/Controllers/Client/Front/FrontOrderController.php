<?php

namespace App\Http\Controllers\Client\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Order\StoreFrontRequest;
use App\Services\Client\Front\FrontOrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FrontOrderController extends Controller
{

    public function __construct(private readonly FrontOrderService $service)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */

    public function index(): View
    {
        $data['page'] = request('page', 1);
        $orders = $this->service->index($data);
        return view('client.order.index', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFrontRequest $request
     * @return RedirectResponse
     */
    public function store(StoreFrontRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $pay_url = $this->service->store($data);
        return redirect()->to($pay_url);
    }

    /**
     * Display the specified resource.
     *
     * @param int $order_id
     * @return View
     */
    public function show(int $order_id): View
    {
        $order = $this->service->show($order_id);
        return view('client.order.show', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $order_id
     * @return RedirectResponse
     */
    public function update(int $order_id): RedirectResponse
    {
        $this->service->update($order_id);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $order_id
     * @return RedirectResponse
     */
    public function destroy(int $order_id): RedirectResponse
    {
        $this->service->destroy($order_id);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $orderPerformer_id
     * @return RedirectResponse
     */
    public function destroyOrderPerformer(int $orderPerformer_id): RedirectResponse
    {
        $this->service->destroyOrderPerformer($orderPerformer_id);
        return back();
    }
}

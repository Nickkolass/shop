<?php

namespace App\Http\Controllers\Client\Front;

use App\Components\HttpClient\HttpClientInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Order\StoreFrontRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FrontOrderController extends Controller
{

    public function __construct(private readonly HttpClientInterface $httpClient)
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
        $orders = $this->httpClient
            ->setUri(route('back.api.orders.index', '', false))
            ->setQuery($data)
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
        $orders = json_decode($orders, true);

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
        $pay_url = $this->httpClient
            ->setUri(route('back.api.orders.store', '', false))
            ->setQuery($data)
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
        session()->forget(['cart', 'filter', 'paginate']);
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
        $order = $this->httpClient
            ->setUri(route('back.api.orders.show', $order_id, false))
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
        $order = json_decode($order, true);
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
        $this->httpClient
            ->setUri(route('back.api.orders.update', $order_id, false))
            ->setMethod('PATCH')
            ->send();
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
        $this->httpClient
            ->setUri(route('back.api.orders.destroy', $order_id, false))
            ->setQuery(['due_to_pay' => request()->input('due_to_pay', false)])
            ->setMethod('DELETE')
            ->send();
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
        $this->httpClient
            ->setMethod('DELETE')
            ->setUri(route('back.api.orders.destroyOrderPerformer', $orderPerformer_id, false))
            ->send();
        return back();
    }

    /**
     * @param int $order_id
     * @return RedirectResponse
     */
    public function pay(int $order_id): RedirectResponse
    {
        $pay_url = $this->httpClient
            ->setMethod('POST')
            ->setUri(route('back.api.orders.pay', $order_id, false))
            ->send()
            ->getBody()
            ->getContents();
        return redirect()->to($pay_url);
    }

    /**
     * @param int $order_id
     * @return RedirectResponse
     */
    public function refund(int $order_id): RedirectResponse
    {
        $this->httpClient
            ->setMethod('POST')
            ->setUri(route('back.api.orders.refund', $order_id, false))
            ->send();
        return back();
    }
}

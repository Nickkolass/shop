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
        $orders = $this->httpClient->request('POST',
            route('back.api.orders.index', '', false),
            ['query' => $data, 'headers' => ['Authorization' => session('jwt')]])
            ->getBody()->getContents();
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
        $payment_url = $this->httpClient->request('POST',
            route('back.api.orders.store', '', false),
            ['query' => $data, 'headers' => ['Authorization' => session('jwt')]])
            ->getBody()->getContents();
        session()->forget(['cart', 'filter', 'paginate']);
        return redirect()->to($payment_url);
    }

    /**
     * Display the specified resource.
     *
     * @param int $order_id
     * @return View
     */
    public function show(int $order_id): View
    {
        $order = $this->httpClient->request('POST',
            route('back.api.orders.show', $order_id, false),
            ['headers' => ['Authorization' => session('jwt')]])
            ->getBody()->getContents();
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
        $this->httpClient->request('PATCH',
            route('back.api.orders.update', $order_id, false),
            ['headers' => ['Authorization' => session('jwt')]]);
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
        $this->httpClient->request('DELETE',
            route('back.api.orders.destroy', $order_id, false),
            ['query' => ['due_to_payment' => request()->input('due_to_payment', false)],
                'headers' => ['Authorization' => session('jwt')]]);
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
        $this->httpClient->request('DELETE',
            route('back.api.orders.destroyOrderPerformer', $orderPerformer_id, false),
            ['headers' => ['Authorization' => session('jwt')]]);
        return back();
    }

    /**
     * @param int $order_id
     * @return RedirectResponse
     */
    public function payment(int $order_id): RedirectResponse
    {
        $payment_url = $this->httpClient->request('POST',
            route('back.api.orders.payment', $order_id, false),
            ['headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        return redirect()->to($payment_url);
    }

    /**
     * @param int $order_id
     * @return RedirectResponse
     */
    public function refund(int $order_id): RedirectResponse
    {
        $this->httpClient->request('POST',
            route('back.api.orders.refund', $order_id, false),
            ['headers' => ['Authorization' => session('jwt')]]);
        return back();
    }
}

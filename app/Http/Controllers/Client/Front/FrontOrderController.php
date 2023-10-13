<?php

namespace App\Http\Controllers\Client\Front;

use App\Components\Guzzle\GuzzleClient;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Order\StoreFrontRequest;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FrontOrderController extends Controller
{

    public function __construct(private readonly GuzzleClient $guzzle)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     * @throws GuzzleException
     */

    public function index(): View
    {
        $data['page'] = request('page', 1);
        $orders = $this->guzzle->client->request('POST', 'api/orders',
            ['query' => $data, 'headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        $orders = json_decode($orders, true);

        return view('client.order.index', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFrontRequest $request
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function store(StoreFrontRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $payment_url = $this->guzzle->client->request('POST', 'api/orders/store',
            ['query' => $data, 'headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        session()->forget(['cart', 'filter', 'paginate']);
        return redirect()->to($payment_url);
    }

    /**
     * @param int $order_id
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function payment(int $order_id): RedirectResponse
    {
        $payment_url = $this->guzzle->client->request('POST', "api/orders/$order_id/payment",
            ['headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        return redirect()->to($payment_url);
    }

    /**
     * Display the specified resource.
     *
     * @param int $order_id
     * @return View
     * @throws GuzzleException
     */
    public function show(int $order_id): View
    {
        $order = $this->guzzle->client->request('POST', 'api/orders/' . $order_id,
            ['headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        $order = json_decode($order, true);
        return view('client.order.show', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $order_id
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function update(int $order_id): RedirectResponse
    {
        $this->guzzle->client->request('PATCH', 'api/orders/' . $order_id,
            ['headers' => ['Authorization' => session('jwt')]]);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $order_id
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function destroy(int $order_id): RedirectResponse
    {
        $this->guzzle->client->request('DELETE', 'api/orders/' . $order_id,
            ['headers' => ['Authorization' => session('jwt')]]);
        return back();
    }
}

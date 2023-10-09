<?php

namespace App\Http\Controllers\Client\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Order\StoreFrontRequest;
use Arhitector\Yandex\Disk;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FrontOrderController extends Controller
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(config('guzzle'));
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

        $orders = $this->client->request('POST', 'api/orders',
            ['query' => $data, 'headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        $orders = json_decode($orders, true);

        return view('client.order.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $total_price = request('total_price');
        $policy = (new Disk(config('services.yandexdisk.oauth_token')))->getResource('Policy.txt')->get('docviewer');
        return view('client.order.create', compact('total_price', 'policy'));
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
        $this->client->request('POST', 'api/orders/store',
            ['query' => $data, 'headers' => ['Authorization' => session('jwt')]]);
        session()->forget(['cart', 'filter', 'paginate']);
        return redirect()->route('client.orders.index');
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
        $order = $this->client->request('POST', 'api/orders/' . $order_id,
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
        $this->client->request('PATCH', 'api/orders/' . $order_id,
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
        $this->client->request('DELETE', 'api/orders/' . $order_id,
            ['headers' => ['Authorization' => session('jwt')]]);
        return back();
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Components\ImportDataClient;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Order\StoreFrontRequest;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FrontOrderController extends Controller
{
    private ImportDataClient $import;

    public function __construct(ImportDataClient $import)
    {
        $this->import = $import;
    }


    /**
     * Display a listing of the resource.
     *
     * @return View
     */

    public function index(): View
    {
        $this->authorize('viewAny', Order::class);

        $data['page'] = request('page') ?? 1;
        $data['user_id'] = auth()->id();

        $orders = $this->import->client->request('POST', 'api/orders', ['query' => $data])->getBody()->getContents();
        $orders = json_decode($orders, true);

        return view('api.order.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    { //нужен route:post но тогда без ресурсного контроллера, поэтому переход только из корзины
        if (url()->previous() != 'http://127.0.0.1:8876/cart' || !session()->has('cart')) abort(404);
        $totalPrice = request('totalPrice');
        return view('api.order.create', compact('totalPrice'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RedirectResponse
     */
    public function store(StoreFrontRequest $request): RedirectResponse
    {
        $this->authorize('create', Order::class);

        $data = $request->validated();
        //платежная система

        $this->import->client->request('POST', 'api/orders/store', ['query' => $data])->getBody()->getContents();
        session()->forget(['cart', 'filter', 'paginate']);

        return redirect()->route('api.orders.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $order_id
     * @return View
     */
    public function show(int $order_id): View
    {
        $this->authorize('view', Order::withTrashed()->find($order_id));
        $order = $this->import->client->request('POST', 'api/orders/' . $order_id)->getBody()->getContents();
        $order = json_decode($order, true);
        return view('api.order.show', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $order_id
     * @return RedirectResponse
     */
    public function update(int $order_id): RedirectResponse
    {
        $this->authorize('update', Order::find($order_id));
        $this->import->client->request('PATCH', 'api/orders/' . $order_id);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $order_id
     * @return RedirectResponse
     */
    public function destroy(int $order_id): RedirectResponse
    {
        $this->authorize('delete', Order::find($order_id));
        $this->import->client->request('DELETE', 'api/orders/' . $order_id)->getBody()->getContents();
        return back();
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Components\ImportDataClient;


class FrontController extends Controller
{
    // $response = Http::get('192.168.32.1:8876/api/products/'.$category, $request);
    public function products($category)
    {
        // session()->forget('filter');
        if (request()->has('page')) {
            $data = session('filter');
            $data['page'] = request()->page;
        } else {
            $data = request()->except('_token');
        }
        $import = new ImportDataClient();
        $products = $import->client->request('POST', 'api/products/' . $category, ['query' => $data])->getBody()->getContents();
        $data = json_decode($products, true)['data'];
        // dd($data);
        array_pop($data['products']['links']);
        array_shift($data['products']['links']);
        $data['cart'] = session('cart');
        session(['filter' => $data['filter']]);
        return view('api.products', compact('data'));
    }

    public function show($category, $product)
    {
        $import = new ImportDataClient();
        $data = $import->client->request('POST', 'api/products/' . $category . '/' . $product)->getBody()->getContents();
        $data = json_decode($data, true)['data'];
        $data['filter'] = session('filter');
        return view('api.product', compact('data'));
    }

    public function addToCart()
    {
        $addToCart = request('addToCart');
        foreach ($addToCart as $k => $v) {
            if (empty($v)) {
                session()->forget('cart.' . $k);
            } else {
                session(['cart.' . $k => $v]);
            }
        }
        return back()->withInput();
    }

    public function cart()
    {
        if (empty(session('cart'))) {
            $data['products'] = [];
        } else {
            $import = new ImportDataClient();
            $data['products'] = $import->client->request('POST', 'api/cart', ['query' => session('cart')])->getBody()->getContents();
            $data['products'] = json_decode($data['products'],  true)['data'];
        }
        return view('api.cart', compact('data'));
    }

    public function order()
    {
        $data['total_price'] = request('total_price');
        return view('api.order', compact('data'));
    }

    public function ordering()
    {
        $data = request()->except('_token');
        $data['user'] = auth()->user()->toArray();
        $data['delivery'] .= '. Получатель: ' . $data['user']['surname'] . ' ' . $data['user']['name'] . ' ' . $data['user']['patronymic'] . '. Адрес: ' . $data['user']['address'];
        $data['user'] = $data['user']['id'];
        $data['cart'] = session('cart');
        //платежная система
        $data['payment_status'] = true;
        $import = new ImportDataClient();
        $import->client->request('POST', 'api/ordering', ['query' => $data])->getBody()->getContents();
        session()->forget(['cart', 'filter']);
        return redirect()->route('api.orders_api');
    }

    public function orders()
    {
        $data['user_id'] = auth()->id();
        $import = new ImportDataClient();
        $data = $import->client->request('POST', 'api/orders', ['query' => $data])->getBody()->getContents();
        $data = json_decode($data, true);
        return view('api.orders', compact('data'));
    }
}

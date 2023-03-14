<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Components\ImportDataClient;

class FrontController extends Controller
{
    // $response = Http::get('192.168.32.1:8876/api/products/'.$category, $request);
    public function index()
    {
        return view('api.index_api');
    }

    public function support()
    {
        return view('api.support');
    }

    public function products($category)
    {
        // dd(request()->except('_token'));
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
        array_pop($data['products']['links']);
        array_shift($data['products']['links']);
        $data['cart'] = session('cart');
        session(['filter' => $data['filter']]);
        // dd($data);
        return view('api.product.index_product', compact('data'));
    }

    public function product($category, $product)
    {
        $import = new ImportDataClient();
        $data = $import->client->request('POST', 'api/products/' . $category . '/' . $product)->getBody()->getContents();
        $data = json_decode($data, true)['data'];
        $data['filter'] = session('filter');
        return view('api.product.show_product', compact('data'));
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
}

<?php

namespace App\Http\Controllers\Client\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Product\ProductsRequest;
use App\Services\Client\Front\FrontService;
use GuzzleHttp\Client;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class FrontProductController extends Controller
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(config('guzzle'));
    }

    public function index(): View|Factory
    {
        $data['viewed'] = array_slice(array_keys(session('viewed') ?? []), 0, 12);

        $data = $this->client->request('POST', 'api/products',
            ['query' => $data, 'headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();

        $data = json_decode($data, true);
        $data['cart'] = session('cart') ?? [];

        return view('client.index', compact('data'));
    }

    public function filter(string $category_title, ProductsRequest $request): Factory|View
    {
        $query_params = $request->validated();
        FrontService::scenarioGetProducts($query_params);

        $data = $this->client->request('POST', '/api/products/' . $category_title,
            ['query' => $query_params, 'headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        $data = json_decode($data, true);
        $product_types = FrontService::afterGetProducts($data);

        return view('client.product.index', compact('data', 'product_types'));
    }

    public function show(int $product_type_id): View|Factory
    {
        $product_type = $this->client->request('POST', 'api/products/show/' . $product_type_id,
            ['headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        $product_type = json_decode($product_type, true);

        $data['page'] = session('paginate.page');
        $data['cart'][$product_type['id']] = session('cart.' . $product_type['id']);
        session(['viewed.' . $product_type_id => '']);

        return view('client.product.show', compact('data', 'product_type'));
    }

    public function cart(): View|Factory
    {
        $product_types = $total_price = null;
        if ($cart = session('cart')) {
            $product_types = $this->client->request('POST', 'api/cart', ['query' => ['cart' => $cart]])->getBody()->getContents();
            $product_types = json_decode($product_types, true);
            $total_price = array_sum(array_column($product_types, 'total_price'));
        }
        return view('client.cart', compact('product_types', 'total_price'));
    }

    public function liked(): View|Factory
    {
        $product_types = $this->client->request('POST', 'api/products/liked',
            ['headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        $product_types = json_decode($product_types, true);
        return view('client.liked', compact('product_types'));
    }
}

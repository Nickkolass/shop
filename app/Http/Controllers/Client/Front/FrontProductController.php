<?php

namespace App\Http\Controllers\Client\Front;

use App\Components\Disk\DiskClientInterface;
use App\Components\HttpClient\HttpClientInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Product\ProductsRequest;
use App\Services\Client\Front\FrontService;
use Illuminate\Contracts\View\View;

class FrontProductController extends Controller
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function index(): View
    {
        $data['viewed'] = array_slice(array_keys(session('viewed', [])), 0, 12);

        $data = $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.products.index', '', false))
            ->setQuery($data)
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();

        $data = json_decode($data, true);
        $data['cart'] = session('cart', []);
        return view('client.index', compact('data'));
    }

    public function filter(string $category_title, ProductsRequest $request): View
    {
        $query_params = $request->validated();
        FrontService::scenarioGetProducts($query_params);

        $data = $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.products.filter', $category_title, false))
            ->setQuery($query_params)
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();

        $data = json_decode($data, true);
        $product_types = FrontService::afterGetProducts($data);

        return view('client.product.index', compact('data', 'product_types'));
    }

    public function show(int $product_type_id): View
    {
        $product_type = $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.products.show', $product_type_id, false))
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
        $product_type = json_decode($product_type, true);

        $data['page'] = session('paginate.page');
        $data['cart'][$product_type['id']] = session('cart.' . $product_type['id']);
        session(['viewed.' . $product_type_id => '']);

        return view('client.product.show', compact('data', 'product_type'));
    }

    public function cart(): View
    {
        $product_types = $total_price = null;
        if ($cart = session('cart')) {
            $product_types = $this->httpClient
            ->setJwt()
                ->setUri(route('back.api.cart', '', false))
                ->setQuery(['cart' => $cart])
                ->setMethod('POST')
                ->send()
                ->getBody()
                ->getContents();
            $product_types = json_decode($product_types, true);
            $total_price = array_sum(array_column($product_types, 'total_price'));
        }
        if (!config('services.yandexdisk.oauth_token')) $policy = 'https://disk.yandex.ru/d/IowD1shlYuOiFw';
        else $policy = app(DiskClientInterface::class)->getResource('Policy.txt')->get('docviewer');

        return view('client.cart', compact('product_types', 'total_price', 'policy'));
    }

    public function liked(): View
    {
        $product_types = $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.products.liked', '', false))
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
        $product_types = json_decode($product_types, true);
        return view('client.liked', compact('product_types'));
    }
}

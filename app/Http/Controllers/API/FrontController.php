<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Product\ProductsRequest;
use App\Http\Requests\API\RatingAndComment\StoreFrontRequest;
use App\Services\API\APIFrontService;
use GuzzleHttp\Client;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FrontController extends Controller
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(config('guzzle'));
    }

    public function index(): View
    {
        $data['viewed'] = array_slice(array_keys(session('viewed') ?? []), 0, 12);

        $data = $this->client->request('POST', 'api/products', ['query' => $data, 'headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();

        $data = json_decode($data, true);
        $data['cart'] = session('cart') ?? [];

        return view('api.index', compact('data'));
    }

    public function products(string $category_title, ProductsRequest $request): View
    {
        $queryParams = $request->validated();
        APIFrontService::scenarioGetProducts($queryParams);

        $data = $this->client->request('POST', '/api/products/' . $category_title, ['query' => $queryParams, 'headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        $data = json_decode($data, true);
        $productTypes = APIFrontService::afterGetProducts($data);

        return view('api.product.index', compact('data', 'productTypes'));
    }


    public function product(string $category_title, int $productType_id): View
    {
        $productType = $this->client->request('POST', 'api/products/' . $category_title . '/' . $productType_id, ['headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        $productType = json_decode($productType, true);

        $data['page'] = session('paginate.page');
        $data['cart'][$productType['id']] = session('cart.' . $productType['id']);
        session(['viewed.' . $productType_id => '']);

        return view('api.product.show', compact('data', 'productType'));
    }


    public function addToCart(): RedirectResponse
    {
        foreach(request('addToCart') as $productType_id => $amount){
            empty($amount) ? session()->forget('cart.' . $productType_id) : session(['cart.' . $productType_id => $amount]);
        }
        return back();
    }


    public function cart(): View
    {
        $productTypes = $totalPrice = null;
        if ($cart = session('cart')) {
            $productTypes = $this->client->request('POST', 'api/cart', ['query' => ['cart' => $cart]])->getBody()->getContents();
            $productTypes = json_decode($productTypes, true);
            $totalPrice = array_sum(array_column($productTypes, 'totalPrice'));
        }
        return view('api.cart', compact('productTypes', 'totalPrice'));
    }

    public function liked(): View
    {
        $productTypes = $this->client->request('POST', 'api/products/liked', ['headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        $productTypes = json_decode($productTypes, true);
        $data['liked_ids'] = array_flip(array_column($productTypes, 'id'));
        return view('api.liked', compact('productTypes', 'data'));
    }

    public function likedToggle(int $productType_id): RedirectResponse
    {
        $this->client->request('POST', 'api/products/liked/' . $productType_id . '/toggle', ['headers' => ['Authorization' => session('jwt')]]);
        return back();
    }


    public function support(): View
    {
        return view('api.support');
    }

    public function commentStore(int $product_id, StoreFrontRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['commentImages'])){
            foreach($data['commentImages'] as &$img) {
                $img = [
                    'path' => $img->getPathname(),
                    'originalName' => $img->getClientOriginalName(),
                    'mimeType' => $img->getClientMimeType(),
                ];
            }
        }
        $this->client->request('POST', 'api/products/' . $product_id . '/comment', ['query' => $data, 'headers' => ['Authorization' => session('jwt')]]);
        return back();
    }

}

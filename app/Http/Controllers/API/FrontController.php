<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Product\ProductsRequest;
use App\Http\Requests\API\RatingAndComment\StoreFrontRequest;
use App\Services\API\APIFrontService;
use GuzzleHttp\Client;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

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

        $data = $this->client->request('POST', 'api/products',
            ['query' => $data, 'headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();

        $data = json_decode($data, true);
        $data['cart'] = session('cart') ?? [];

        return view('api.index', compact('data'));
    }

    public function productIndex(string $category_title, ProductsRequest $request)
    {
        $query_params = $request->validated();
        APIFrontService::scenarioGetProducts($query_params);

        $data = $this->client->request('POST', '/api/products/' . $category_title,
            ['query' => $query_params, 'headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        $data = json_decode($data, true);
        $product_types = APIFrontService::afterGetProducts($data);

        return view('api.product.index', compact('data', 'product_types'));
    }

    public function productShow(int $product_type_id): View
    {
        $product_type = $this->client->request('POST', 'api/products/show/' . $product_type_id,
            ['headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        $product_type = json_decode($product_type, true);

        $data['page'] = session('paginate.page');
        $data['cart'][$product_type['id']] = session('cart.' . $product_type['id']);
        session(['viewed.' . $product_type_id => '']);

        return view('api.product.show', compact('data', 'product_type'));
    }

    public function addToCart(): RedirectResponse
    {
        $prev_name = Route::getRoutes()->match(request()->create(url()->previousPath()))->getName();
        if ($prev_name == 'api.products') session(['backFilter' => true]);
        foreach (request('addToCart') as $product_type_id => $amount) {
            empty($amount) ? session()->forget('cart.' . $product_type_id) : session(['cart.' . $product_type_id => $amount]);
        }
        return back();
    }

    public function cart(): View
    {
        $product_types = $total_price = null;
        if ($cart = session('cart')) {
            $product_types = $this->client->request('POST', 'api/cart', ['query' => ['cart' => $cart]])->getBody()->getContents();
            $product_types = json_decode($product_types, true);
            $total_price = array_sum(array_column($product_types, 'total_price'));
        }
        return view('api.cart', compact('product_types', 'total_price'));
    }

    public function likedProducts(): View
    {
        $product_types = $this->client->request('POST', 'api/products/liked',
            ['headers' => ['Authorization' => session('jwt')]])->getBody()->getContents();
        $product_types = json_decode($product_types, true);
        $data['liked_ids'] = array_flip(array_column($product_types, 'id'));
        return view('api.liked', compact('product_types', 'data'));
    }

    public function likedToggle(int $product_type_id): RedirectResponse
    {
        $prev_name = Route::getRoutes()->match(request()->create(url()->previousPath()))->getName();
        if ($prev_name == 'api.products') session(['backFilter' => true]);
        $this->client->request('POST', 'api/products/liked/' . $product_type_id,
            ['headers' => ['Authorization' => session('jwt')]]);
        return back();
    }

    public function commentStore(int $product_id, StoreFrontRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (!empty($data['comment_images'])) APIFrontService::imgEncode($data);
        $this->client->request('POST', 'api/products/' . $product_id . '/comment',
            ['query' => $data, 'headers' => ['Authorization' => session('jwt')]]);
        return back();
    }
}

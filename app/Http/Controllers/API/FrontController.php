<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Components\ImportDataClient;
use App\Http\Requests\API\Product\ProductsRequest;
use App\Services\API\APIFrontService;

class FrontController extends Controller
{

    private $import;

    public function __construct(ImportDataClient $import)
    {
        $this->import = $import;
    }


    // $response = Http::get('192.168.32.1:8876/api/products/'.$category, $request);
    public function index()
    {
        $productTypes = $data['cart'] = null;
        if ($viewed = session('viewed')) {
            $viewed = array_slice(value(array_unique(array_reverse($viewed))), 0, 12);
            $productTypes = $this->import->client->request('POST', 'api/products', ['query' => ['viewed' => $viewed]])->getBody()->getContents();
            $productTypes = json_decode($productTypes, true);
            $data['cart'] = session('cart') ?? [];
        }

        return view('api.index_api', compact('data', 'productTypes'));
    }


    public function products($category, ProductsRequest $request)
    {
        $queryParams = $request->validated();

        APIFrontService::scenarioGetProducts($queryParams);

        $data = $this->import->client->request('POST', 'api/products/' . $category, ['query' => $queryParams])->getBody()->getContents();
        $data = json_decode($data, true);
        
        $productTypes = APIFrontService::afterGetProducts($data);

        return view('api.product.index_product', compact('data', 'productTypes'));
    }


    public function product($category, $productType_id)
    {
        $productType = $this->import->client->request('POST', 'api/products/' . $category . '/' . $productType_id)->getBody()->getContents();
        $productType = json_decode($productType, true);

        $data['page'] = session('paginate.page');
        $data['cart'][$productType['id']] = session('cart.'. $productType['id']);
        session()->push('viewed', $productType_id);

        return view('api.product.show_product', compact('data', 'productType'));
    }


    public function addToCart()
    {
        foreach(request('addToCart') as $productType_id => $amount){
            empty($amount) ? session()->forget('cart.' . $productType_id) : session(['cart.' . $productType_id => $amount]);
        }
        return back();
        // APIFrontService::scenarioAddToCart($addToCart);
    }


    public function cart()
    {
        $productTypes = $totalPrice = null;
        if ($cart = session('cart')) {
            $productTypes = $this->import->client->request('POST', 'api/cart', ['query' => ['cart' => $cart]])->getBody()->getContents();
            $productTypes = json_decode($productTypes,  true);
            $totalPrice = array_sum(array_column($productTypes, 'totalPrice'));
        }
        return view('api.cart', compact('productTypes', 'totalPrice'));
    }


    public function support()
    {
        return view('api.support');
    }
}

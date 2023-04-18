<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Components\ImportDataClient;
use App\Http\Requests\API\Product\ProductsRequest;
use App\Services\API\APIFrontService;
use Illuminate\Http\Request;

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
        $data['products'] = session('viewed') ?? [];
        $data['cart'] = session('cart') ?? [];

        if (!empty($data['products'])) {
            $products = $this->import->client->request('POST', 'api/products', ['query' => $data])->getBody()->getContents();
            $data['products'] = json_decode($products, true);
        }

        return view('api.index_api', compact('data'));
    }


    public function products($category, ProductsRequest $request)
    {
        $queryParams = $request->validated();

        APIFrontService::scenarioGetProducts($queryParams);

        $data = $this->import->client->request('POST', 'api/products/' . $category, ['query' => $queryParams])->getBody()->getContents();
        $data = json_decode($data, true);

        $data = APIFrontService::afterGetProducts($data);

        return view('api.product.index_product', compact('data'));
    }


    public function product($category, $product_id)
    {
        $product = $this->import->client->request('POST', 'api/products/' . $category . '/' . $product_id, ['query' => session('cart')])->getBody()->getContents();
        $product = json_decode($product, true);
       
        $page = session('paginate.page');
        session()->push('viewed', $product_id);

        return view('api.product.show_product', compact('product', 'page'));
    }


    public function addToCart()
    {
        $addToCart = request('addToCart');
        $addToCart = array_pop($addToCart);
        $addToCart['amount'] = array_pop($addToCart['amount']);

        APIFrontService::scenarioAddToCart($addToCart);
        return back()->withInput();
    }


    public function cart()
    {
        $products = [];
        if ($cart = session('cart')) {
            $products = $this->import->client->request('POST', 'api/cart', ['query' => $cart])->getBody()->getContents();
            $products = json_decode($products,  true);
        }
        return view('api.cart', compact('products'));
    }

    
    public function support()
    {
        return view('api.support');
    }
}

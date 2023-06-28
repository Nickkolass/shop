<?php

namespace App\Http\Controllers\API;

use App\Components\ImportDataClient;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Product\ProductsRequest;
use App\Http\Requests\API\RatingAndComment\StoreRequest;
use App\Models\User;
use App\Services\API\APIFrontService;
use Illuminate\Http\UploadedFile;

class FrontController extends Controller
{

    private $import;

    public function __construct(ImportDataClient $import)
    {
        $this->import = $import;
    // $response = Http::get('192.168.32.1:8876/api/products/'.$category, $request);
    }
    public function index()
    {
        $data['user_id'] = auth()->id();
        $data['viewed'] = array_slice(array_keys(session('viewed') ?? []), 0, 12);

        $data = $this->import->client->request('POST', 'api/products', ['query' => $data])->getBody()->getContents();

        $data = json_decode($data, true);
        $data['cart'] = session('cart') ?? [];

        return view('api.index', compact('data'));
    }


    public function products($category, ProductsRequest $request)
    {
        $queryParams = $request->validated();
        APIFrontService::scenarioGetProducts($queryParams);

        $data = $this->import->client->request('POST', 'api/products/' . $category, ['query' => $queryParams])->getBody()->getContents();
        $data = json_decode($data, true);
        $productTypes = APIFrontService::afterGetProducts($data);

        return view('api.product.index', compact('data', 'productTypes'));
    }


    public function product($category, $productType_id)
    {
        $productType = $this->import->client->request('POST', 'api/products/' . $category . '/' . $productType_id)->getBody()->getContents();
        $productType = json_decode($productType, true);

        $data['page'] = session('paginate.page');
        $data['cart'][$productType['id']] = session('cart.' . $productType['id']);
        session(['viewed.' . $productType_id => '']);

        return view('api.product.show', compact('data', 'productType'));
    }


    public function addToCart()
    {
        foreach(request('addToCart') as $productType_id => $amount){
            empty($amount) ? session()->forget('cart.' . $productType_id) : session(['cart.' . $productType_id => $amount]);
        }
        return back();
    }


    public function cart()
    {
        $productTypes = $totalPrice = null;
        if ($cart = session('cart')) {
            $productTypes = $this->import->client->request('POST', 'api/cart', ['query' => ['cart' => $cart]])->getBody()->getContents();
            $productTypes = json_decode($productTypes, true);
            $totalPrice = array_sum(array_column($productTypes, 'totalPrice'));
        }
        return view('api.cart', compact('productTypes', 'totalPrice'));
    }

    public function liked()
    {
        $this->authorize('like', User::class);
        $productTypes = $this->import->client->request('POST', 'api/products/liked', ['query' => ['user_id' => auth()->id()]])->getBody()->getContents();
        $productTypes = json_decode($productTypes, true);
        $data['liked_ids'] = array_flip(array_column($productTypes, 'id'));
        return view('api.liked', compact('productTypes', 'data'));
    }

    public function likedToggle($productType_id)
    {
        $this->authorize('like', User::class);
        $this->import->client->request('POST', 'api/products/liked/' . $productType_id . '/toggle', ['query' => ['user_id' => auth()->id()]]);
        return back();
    }


    public function support()
    {
        return view('api.support');
    }

    public function commentStore($product_id, StoreRequest $request)
    {
        $this->authorize('like', User::class);
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

        $this->import->client->request('POST', 'api/products/' . $product_id . '/comment', ['query' => $data]);
        return back();
    }

}

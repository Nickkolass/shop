<?php

namespace App\Http\Controllers\Client\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RatingAndComment\StoreFrontRequest;
use App\Services\Client\Front\FrontService;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

class FrontUserActiveController extends Controller
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(config('guzzle'));
    }

    public function addToCart(): RedirectResponse
    {
        $prev_name = Route::getRoutes()->match(request()->create(url()->previousPath()))->getName();
        if ($prev_name == 'api.products.filter') session(['backFilter' => true]);
        foreach (request('addToCart') as $product_type_id => $amount) {
            empty($amount) ? session()->forget('cart.' . $product_type_id) : session(['cart.' . $product_type_id => $amount]);
        }
        return back();
    }

    public function likedToggle(int $product_type_id): RedirectResponse
    {
        $prev_name = Route::getRoutes()->match(request()->create(url()->previousPath()))->getName();
        if ($prev_name == 'api.products.filter') session(['backFilter' => true]);
        $this->client->request('POST', 'api/products/liked/' . $product_type_id,
            ['headers' => ['Authorization' => session('jwt')]]);
        return back();
    }

    public function commentStore(int $product_id, StoreFrontRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (!empty($data['comment_images'])) FrontService::imgEncode($data['comment_images']);
        $this->client->request('POST', 'api/products/' . $product_id . '/comment',
            ['query' => $data, 'headers' => ['Authorization' => session('jwt')]]);
        return back();
    }
}

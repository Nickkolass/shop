<?php

namespace App\Http\Controllers\Client\Front;

use App\Components\HttpClient\HttpClientInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\RatingAndComment\StoreFrontRequest;
use App\Services\Client\Front\FrontService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

class FrontUserActiveController extends Controller
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function addToCart(): RedirectResponse
    {
        $prev_name = Route::getRoutes()->match(request()->create(url()->previousPath()))->getName();
        if ($prev_name == 'client.products.filter') session(['backFilter' => true]);
        foreach (request('addToCart') as $product_type_id => $amount) {
            empty($amount) ? session()->forget('cart.' . $product_type_id) : session(['cart.' . $product_type_id => $amount]);
        }
        return back();
    }

    public function likedToggle(int $product_type_id): RedirectResponse
    {
        $prev_name = Route::getRoutes()->match(request()->create(url()->previousPath()))->getName();
        if ($prev_name == 'client.products.filter') session(['backFilter' => true]);
        $this->httpClient->request('POST',
            route('back.api.products.likedToggle', $product_type_id, false),
            ['headers' => ['Authorization' => session('jwt')]]);
        return back();
    }

    public function commentStore(int $product_id, StoreFrontRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (!empty($data['comment_images'])) FrontService::imgEncode($data['comment_images']);
        $this->httpClient->request('POST',
            route('back.api.products.commentStore', $product_id, false),
            ['query' => $data, 'headers' => ['Authorization' => session('jwt')]]);
        return back();
    }
}

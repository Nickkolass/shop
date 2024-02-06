<?php

namespace App\Http\Controllers\Client\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\RatingAndComment\StoreFrontRequest;
use App\Services\Client\Front\FrontUserActiveService;
use Illuminate\Http\RedirectResponse;

class FrontUserActiveController extends Controller
{
    public function __construct(private readonly FrontUserActiveService $service)
    {
    }

    public function addToCart(): RedirectResponse
    {
        $this->service->addToCart();
        return back();
    }

    public function likedToggle(int $product_type_id): RedirectResponse
    {
        $this->service->likedToggle($product_type_id);
        return back();
    }

    public function commentStore(StoreFrontRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->commentStore($data);
        return back();
    }
}

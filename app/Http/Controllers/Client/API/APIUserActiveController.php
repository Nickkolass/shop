<?php

namespace App\Http\Controllers\Client\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RatingAndComment\StoreRequest;
use App\Models\ProductType;
use App\Models\User;
use App\Services\Client\API\UserActiveService;

class APIUserActiveController extends Controller
{

    public function __construct(private readonly UserActiveService $service)
    {
    }

    public function likedToggle(ProductType $productType): void
    {
        $this->authorize('product', User::class);
        $this->service->likedToggle($productType, auth('api')->id());
    }

    public function commentStore(StoreRequest $request): void
    {
        $this->authorize('product', User::class);
        $data = $request->validated();
        $this->service->commentStore($data);
    }
}

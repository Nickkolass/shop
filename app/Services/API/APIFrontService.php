<?php

namespace App\Services\API;


use App\Models\ProductType;

class APIFrontService
{
    public static function scenarioGetProducts(?array &$queryParams): void
    {
        if (!empty($queryParams['page'])) {
            $queryParams['filter'] = session('filter') ?? null;
            $queryParams['paginate'] = session('paginate');
            $queryParams['paginate']['page'] = $queryParams['page'];
        } elseif (!empty($queryParams['filter']) || !empty($queryParams['paginate'])) {
            $queryParams['filter'] = $queryParams['filter'] ?? null;
            $queryParams['paginate'] = $queryParams['paginate'] ?? null;
        }
        $queryParams['cart'] = session('cart');
        $queryParams['user_id'] = auth()->id();
    }


    public static function afterGetProducts(array &$data): ?array
    {
        $data['cart'] = session('cart');
        session(['filter' => $data['filter']]);
        session(['paginate' => $data['paginate']]);
        $productTypes = $data['productTypes'];
        unset($data['productTypes']);
        return $productTypes;
    }
}

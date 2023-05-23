<?php

namespace App\Services\API;


class APIFrontService
{
    public static function scenarioGetProducts(&$queryParams)
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
    }


    public static function afterGetProducts(&$data)
    {
        $data['cart'] = session('cart');
        session(['filter' => $data['filter']]);
        session(['paginate' => $data['paginate']]);
        $productTypes = $data['productTypes'];
        unset($data['productTypes']);
        return $productTypes;
    }
}

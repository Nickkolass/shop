<?php

namespace App\Services\API;


class APIFrontService
{
    public static function scenarioGetProducts(?array &$queryParams): void
    {
        if (!empty($queryParams['page']) || session()->pull('backFilter')) {
            //смена страницы либо добавление в корзину или избранное со страницы api.products
            $queryParams['filter'] = session('filter') ?? null;
            $queryParams['paginate'] = session('paginate');
            $queryParams['paginate']['page'] = $queryParams['page'] ?? $queryParams['paginate']['page'];
        } elseif (!empty($queryParams['filter']) || !empty($queryParams['paginate'])) {
            //применение фильтра
            $queryParams['filter'] = $queryParams['filter'] ?? null;
            $queryParams['paginate'] = $queryParams['paginate'] ?? null;
        }
        // переход на страницу api.products
        $queryParams['cart'] = session('cart');
    }


    public static function afterGetProducts(array &$data): ?array
    {
        $data['cart'] = session('cart');
        session(['filter' => $data['filter'], 'paginate' => $data['paginate']]);
        $productTypes = $data['productTypes'];
        unset($data['productTypes']);
        return $productTypes;
    }

    public static function imgEncode(array &$data): void
    {
        if (!empty($data['commentImages'])) {
            foreach ($data['commentImages'] as &$img) {
                $img = [
                    'path' => $img->getPathname(),
                    'originalName' => $img->getClientOriginalName(),
                    'mimeType' => $img->getClientMimeType(),
                ];
            }
        }
    }
}

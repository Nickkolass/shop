<?php

namespace App\Services\Client\Front;

class FrontService
{
    public static function scenarioGetProducts(?array &$query_params): void
    {
        if (!empty($query_params['page']) || session()->pull('backFilter')) {
            //смена страницы либо добавление в корзину или избранное со страницы api.products
            $query_params['filter'] = session('filter') ?? null;
            $query_params['paginate'] = session('paginate');
            $query_params['paginate']['page'] = $query_params['page'] ?? $query_params['paginate']['page'];
        } elseif (!empty($query_params['filter']) || !empty($query_params['paginate'])) {
            //применение фильтра
            $query_params['filter'] = $query_params['filter'] ?? null;
            $query_params['paginate'] = $query_params['paginate'] ?? null;
        }
        // переход на страницу api.products
        $query_params['cart'] = session('cart');
    }

    public static function afterGetProducts(array &$data): ?array
    {
        $data['cart'] = session('cart');
        session(['filter' => $data['filter'], 'paginate' => $data['paginate']]);
        $product_types = $data['product_types'];
        unset($data['product_types']);
        return $product_types;
    }

    public static function imgEncode(array &$data): void
    {
        foreach ($data['comment_images'] as &$img) {
            $img = [
                'path' => $img->getPathname(),
                'original_name' => $img->getClientOriginalName(),
                'mime_type' => $img->getClientMimeType(),
            ];
        }
    }
}
<?php

namespace App\Services\Client\Front;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class FrontService
{
    /**
     * @param null|array<mixed> &$query_params
     * @return void
     */
    public static function scenarioGetProducts(?array &$query_params): void
    {
        if (!empty($query_params['page']) || session()->pull('backFilter')) {
            //смена страницы либо добавление в корзину или избранное со страницы api.products
            $query_params['filter'] = session('filter');
            $query_params['paginate'] = session('paginate');
            $query_params['paginate']['page'] = $query_params['page'] ?? $query_params['paginate']['page'];
        } elseif (!empty($query_params)) {
            //применение фильтра
            $query_params['filter'] = $query_params['filter'] ?? null;
            $query_params['paginate'] = $query_params['paginate'] ?? null;
        }
        // переход на страницу api.products
        $query_params['cart'] = session('cart');
    }

    /**
     * @param array<mixed> &$data
     * @return null|array<array<mixed>>
     */
    public static function afterGetProducts(array &$data): ?array
    {
        $data['cart'] = session('cart');
        session(['filter' => $data['filter'], 'paginate' => $data['paginate']]);
        return Arr::pull($data, 'product_types');
    }

    /**
     * @param array<UploadedFile> &$images
     * @return void
     */
    public static function imgEncode(array &$images): void
    {
        foreach ($images as &$img) {
            $img = [
                'path' => $img->getPathname(),
                'originalName' => $img->getClientOriginalName(),
                'mimeType' => $img->getClientMimeType(),
            ];
        }
    }
}

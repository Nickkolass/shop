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


    public static function afterGetProducts($data)
    {
        $data['cart'] = session('cart');

        session(['filter' => $data['filter']]);
        session(['paginate' => $data['paginate']]);

        return $data;
    }


    public static function scenarioAddToCart($addToCart)
    {
        //если обновление из корзины
        if (!empty($addToCart['cart_id'])) {
            a:
            $addToCart['amount'] == 0 ? session()->forget('cart.' . $addToCart['cart_id'])
                : session(['cart.' . $addToCart['cart_id'] . '.amount' => $addToCart['amount']]);
            return;
        }

        if ($cart = session('cart')) {
            foreach ($cart as $key => $val) {
                $val['product_id'] != $addToCart['product_id'] ?: $inCart[$key] = $val;
            }

            if (!empty($inCart)) {
                if (!in_array($addToCart, $inCart)) {
                    foreach ($inCart as $k => $v) {
                        if ($addToCart['optionValues'] == $v['optionValues']) {
                            $addToCart['cart_id'] = $k;
                            goto a;
                        }
                    }
                } else return;
            }
        }
        empty($addToCart['amount']) ?: session()->push('cart', $addToCart);
    }
}

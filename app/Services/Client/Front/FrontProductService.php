<?php

namespace App\Services\Client\Front;

use App\Components\Disk\DiskClientInterface;
use App\Components\Transport\Protokol\Http\HttpClientInterface;
use Illuminate\Support\Arr;

class FrontProductService
{

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    /** @return array<mixed> */
    public function index(): array
    {
        $data['viewed'] = array_slice(array_keys(session('viewed', [])), 0, 12);

        $res = $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.products.index', '', false))
            ->setQuery($data)
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();

        $res = json_decode($res, true);
        $res['cart'] = session('cart', []);
        return $res;
    }

    /**
     * @param string $category_title
     * @param array<mixed> $query_params
     * @param array{} $data
     * @return array<mixed>
     */
    public function filter(string $category_title, array $query_params, array &$data): array
    {
        $this->scenarioGetProducts($query_params);

        $data = $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.products.filter', $category_title, false))
            ->setQuery($query_params)
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();

        $data = json_decode($data, true);
        $data['cart'] = session('cart');
        session(['filter' => $data['filter'], 'paginate' => $data['paginate']]);
        return Arr::pull($data, 'product_types');
    }

    /**
     * @param null|array<mixed> &$query_params
     * @return void
     */
    private function scenarioGetProducts(?array &$query_params): void
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
     * @param int $product_type_id
     * @param array{} $data
     * @return array<mixed>
     */
    public function show(int $product_type_id, array &$data): array
    {
        $product_type = $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.products.show', $product_type_id, false))
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
        $product_type = json_decode($product_type, true);

        $data['page'] = session('paginate.page');
        $data['cart'][$product_type_id] = session('cart.' . $product_type_id);
        session(['viewed.' . $product_type_id => '']);

        return $product_type;
    }

    /** @return array<mixed> */
    public function cart(): array
    {
        $product_types = $total_price = null;
        if ($cart = session('cart')) {
            $product_types = $this->httpClient
                ->setJwt()
                ->setUri(route('back.api.cart', '', false))
                ->setQuery(['cart' => $cart])
                ->setMethod('POST')
                ->send()
                ->getBody()
                ->getContents();
            $product_types = json_decode($product_types, true);
            $total_price = array_sum(array_column($product_types, 'total_price'));
        }
        if (!config('services.yandexdisk.oauth_token')) $policy = 'https://disk.yandex.ru/d/IowD1shlYuOiFw';
        else $policy = app(DiskClientInterface::class)->getResource('Policy.txt')->get('docviewer');

        return [
            'product_types' => $product_types,
            'total_price' => $total_price,
            'policy' => $policy,
        ];
    }

    /** @return array<mixed> */
    public function liked(): array
    {
        $product_types = $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.products.liked', '', false))
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
        return (array)json_decode($product_types, true);
    }
}

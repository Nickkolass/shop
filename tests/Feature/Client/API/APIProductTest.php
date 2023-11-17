<?php

namespace Client\API;

use App\Models\Category;
use App\Models\ProductType;
use App\Models\User;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class APIProductTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_can_getting_data_for_cart_url(): void
    {
        $this->withoutExceptionHandling();
        $route = route('back.api.cart');

        $res = $this->post($route, ['cart' => []]);
        $res->assertOk();
        $res->assertJsonCount(0);

        $res = $this->post($route, ['cart' => [ProductType::query()->first()->id => 1]]);
        $res->assertOk();
        $res->assertJsonCount(1);
    }

    /**@test */
    public function test_can_getting_data_for_products_index_url(): void
    {
        $viewed = ProductType::query()->take(2)->pluck('id')->all();
        $route = route('back.api.products.index');
        $user = User::query()->first();
        $jwt = $this->getJwt($user);

        $this->withoutExceptionHandling();

        $res = $this->post($route);
        $res->assertOk();
        $res->assertJsonStructure();

        $res = $this->post($route, ['viewed' => $viewed]);
        $res->assertOk();
        $res->assertJsonCount(2, 'viewed');
        $res->assertJsonMissing(['liked']);

        $this->withHeader('Authorization', $jwt)
            ->post($route)
            ->assertOk()
            ->assertJsonMissing(['viewed'])
            ->assertJsonCount($user->liked()->count(), 'liked');

        $this->withHeader('Authorization', $jwt)
            ->post($route, ['viewed' => $viewed])
            ->assertOk()
            ->assertJsonCount(2, 'viewed')
            ->assertJsonCount($user->liked()->count(), 'liked');
    }

    /**@test */
    public function test_can_getting_data_for_products_filter_url(): void
    {
        $this->withoutExceptionHandling();
        $route = route('back.api.products.filter', Category::query()->has('products')->first()->title);
//        посещение страницы
        $res = $this->post($route)
            ->assertOk()
            ->assertJsonStructure(['product_types', 'paginate', 'filter', 'filterable', 'category'])
            ->getContent();

        $res = json_decode((string)$res, true);
        $this->assertNotEmpty($res['product_types']);
        $this->assertNotEmpty($res['product_types']['data']);
        $this->assertNotEmpty($res['paginate']);
        $this->assertEmpty($res['filter']);
        $this->assertNotEmpty($res['filterable']);
        $this->assertNotEmpty($res['category']);
        $this->assertTrue($res['product_types']['current_page'] == 1);

//        пагинация
        $data = ['paginate' => ['page' => 2]];
        $res = $this->post($route, $data)
            ->assertOk()
            ->assertJsonStructure(['product_types', 'paginate', 'filter', 'filterable', 'category'])
            ->getContent();

        $res = json_decode((string)$res, true);
        $this->assertNotEmpty($res['product_types']);
        $this->assertNotEmpty($res['paginate']);
        $this->assertEmpty($res['filter']);
        $this->assertNotEmpty($res['filterable']);
        $this->assertNotEmpty($res['category']);
        $this->assertTrue($res['product_types']['current_page'] == 2);

//        фильтр
        $data = ['filter' => ['salers' => User::query()->take(5)->pluck('id')->all()], 'paginate' => ['perPage' => 4, 'orderBy' => 'ASC']];
        $res = $this->post($route, $data)
            ->assertOk()
            ->assertJsonStructure(['product_types', 'paginate', 'filter', 'filterable', 'category'])
            ->getContent();

        $res = json_decode((string)$res, true);
        $this->assertNotEmpty($res['product_types']);
        $this->assertNotEmpty($res['paginate']);
        $this->assertNotEmpty($res['filter']);
        $this->assertNotEmpty($res['filterable']);
        $this->assertNotEmpty($res['category']);
        $this->assertTrue($res['product_types']['current_page'] == 1);
        $this->assertTrue($res['product_types']['per_page'] == 4);
    }

    /**@test */
    public function test_can_getting_data_for_liked_url(): void
    {
        $route = route('back.api.products.liked');

        $this->post($route)->assertUnauthorized();

        $this->withoutExceptionHandling();

        $user = User::query()->first();
        $jwt = $this->getJwt($user);
        $this->withHeader('Authorization', $jwt)
            ->post($route)
            ->assertOk()
            ->assertJsonCount($user->liked()->count());
    }

    /**@test */
    public function test_can_getting_data_for_product_show_url(): void
    {
        $productType_id = ProductType::query()->first()->id;
        $route = route('back.api.products.show', $productType_id);

        $this->withoutExceptionHandling();

        $this->post($route)
            ->assertOk()
            ->assertJsonCount(10);
    }
}

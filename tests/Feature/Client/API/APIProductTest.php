<?php

namespace Client\API;

use App\Models\Category;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class APIProductTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        View::share('categories', Category::all()->toArray());
    }

    protected function tearDown(): void
    {
        foreach(Storage::directories() as $dir) if($dir != 'factory') Storage::deleteDirectory($dir);
        parent::tearDown();
    }

    /**@test */
    public function test_can_getting_data_for_cart_url()
    {
        $this->withoutExceptionHandling();

        $res = $this->post('/api/cart', ['cart' => []]);
        $res->assertOk();
        $res->assertJsonCount(0);

        $res = $this->post('/api/cart', ['cart' => [ProductType::first()->id => 1]]);
        $res->assertOk();
        $res->assertJsonCount(1);
    }

    /**@test */
    public function test_can_getting_data_for_products_index_url()
    {
        $viewed = ProductType::take(2)->pluck('id')->all();

        $this->withoutExceptionHandling();

        $res = $this->post('/api/products', ['viewed' => $viewed]);
        $res->assertOk();
        $res->assertJsonCount(2, 'viewed');
        $res->assertJsonMissing(['liked']);

        $user = User::first();
        $this->actingAs($user)->get('/');
        $res = $this->withHeader('Authorization', session('jwt'))->post('/api/products', ['viewed' => $viewed]);
        $res->assertOk();
        $res->assertJsonCount(2, 'viewed');
        $count = $user->liked()->count();
        $res->assertJsonCount($count, 'liked');
    }

    /**@test */
    public function test_can_getting_data_for_products_filter_url()
    {
        $this->withoutExceptionHandling();

//        посещение страницы
        $res = $this->post('/api/products/' . Category::first()->title);
        $res->assertOk();
        $res->assertJsonStructure(['product_types', 'paginate', 'filter', 'filterable', 'category']);
        $res->assertJsonCount(8, 'product_types.data');
        $res->assertJsonCount(10, 'product_types.data.0');

//        пагинация
        $data = ['paginate' => ['page' => 3]];
        $res = $this->post('/api/products/' . Category::first()->title, $data);
        $res->assertOk();
        $res->assertJsonStructure(['product_types', 'paginate', 'filter', 'filterable', 'category']);
        $res->assertJsonCount(4, 'product_types.data');
        $res->assertJsonCount(10, 'product_types.data.0');

//        фильтр
        $data = ['filter' => ['salers' => User::take(5)->pluck('id')->all()], 'paginate' => ['perPage' => 4, 'orderBy' => 'ASC']];
        $res = $this->post('/api/products/' . Category::first()->title, $data);
        $res->assertOk();
        $res->assertJsonStructure(['product_types', 'paginate', 'filter', 'filterable', 'category']);
        $res->assertJsonCount(4, 'product_types.data');
        $res->assertJsonCount(10, 'product_types.data.0');
    }

    /**@test */
    public function test_can_getting_data_for_liked_url()
    {
        $this->post('/api/products/liked')->assertUnauthorized();

        $this->withoutExceptionHandling();

        $user = User::first();
        $this->actingAs($user)->get('/');
        $res = $this->withHeader('Authorization', session('jwt'))->post('/api/products/liked');
        $res->assertOk();
        $res->assertJsonCount($user->liked()->count());
    }

    /**@test */
    public function test_can_getting_data_for_product_show_url()
    {
        $productType_id = ProductType::first()->id;

        $this->withoutExceptionHandling();

        $res = $this->post('/api/products/show/' . $productType_id);
        $res->assertOk();
        $res->assertJsonCount(10);
    }
}

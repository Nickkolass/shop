<?php

namespace Tests\Feature\API;

use App\Models\Category;
use App\Models\CommentImage;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class BackTest extends TestCase
{

    use RefreshDatabase;

    /**@test */
    public function test_can_getting_data_for_cart_url()
    {
        $this->seed();
        View::share('categories', Category::all()->toArray());

        $this->withoutExceptionHandling();

        $res = $this->post('/api/cart', ['cart' => []]);
        $res->assertOk();
        $res->assertJsonCount(0);

        $res = $this->post('/api/cart', ['cart' => [ProductType::first()->id => 1]]);
        $res->assertOk();
        $res->assertJsonCount(1);
    }

    /**@test */
    public function test_can_getting_data_for_index_url()
    {
        $this->seed();
        View::share('categories', Category::all()->toArray());
        $viewed = ProductType::take(2)->pluck('id')->all();

        $this->withoutExceptionHandling();

        $res = $this->post('/api/products', ['viewed' => $viewed]);
        $res->assertOk();
        $res->assertJsonCount(2, 'viewed');
        $res->assertJsonMissing(['liked', 'liked_ids']);

        $user = User::first();
        $this->actingAs($user)->get('/');
        $res = $this->withHeader('Authorization', session('jwt'))->post('/api/products', ['viewed' => $viewed]);
        $res->assertOk();
        $res->assertJsonCount(2, 'viewed');
        $count = $user->liked()->count();
        $res->assertJsonCount($count, 'liked_ids');
        $res->assertJsonCount($count, 'liked');
    }

    /**@test */
    public function test_can_getting_data_for_products_category_url()
    {
        $this->seed();
        View::share('categories', Category::all()->toArray());

        $this->withoutExceptionHandling();

        $res = $this->post('/api/products/' . Category::first()->title);
        $res->assertOk();
        $res->assertJsonStructure(['productTypes', 'paginate', 'filter', 'filterable', 'category', 'liked_ids']);
        $res->assertJsonCount(8, 'productTypes.data');
        $res->assertJsonCount(9, 'productTypes.data.0');

//        посещение страницы
        $res = $this->post('/api/products/' . Category::first()->title);
        $res->assertOk();
        $res->assertJsonStructure(['productTypes', 'paginate', 'filter', 'filterable', 'category', 'liked_ids']);
        $res->assertJsonCount(8, 'productTypes.data');
        $res->assertJsonCount(9, 'productTypes.data.0');

//        переход на другую страницу
        $data = ['paginate' => ['page' => 3]];
        $res = $this->post('/api/products/' . Category::first()->title, $data);
        $res->assertOk();
        $res->assertJsonStructure(['productTypes', 'paginate', 'filter', 'filterable', 'category', 'liked_ids']);
        $res->assertJsonCount(4, 'productTypes.data');
        $res->assertJsonCount(9, 'productTypes.data.0');

//        фильтр
        $data = ['filter' => ['salers' => User::take(5)->pluck('id')->all()], 'paginate' => ['perPage' => 4, 'orderBy' => 'ASC']];
        $res = $this->post('/api/products/' . Category::first()->title, $data);
        $res->assertOk();
        $res->assertJsonStructure(['productTypes', 'paginate', 'filter', 'filterable', 'category', 'liked_ids']);
        $res->assertJsonCount(4, 'productTypes.data');
        $res->assertJsonCount(9, 'productTypes.data.0');
    }

    /**@test */
    public function test_can_getting_data_for_liked_url()
    {
        $this->seed();
        View::share('categories', Category::all()->toArray());

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
        $this->seed();
        View::share('categories', Category::all()->toArray());
        $productType_id = ProductType::first()->id;

        $this->withoutExceptionHandling();

        $res = $this->post('/api/products/show/' . $productType_id);
        $res->assertOk();
        $res->assertJsonCount(10);
    }

    /**@test */
    public function test_a_product_can_be_liked()
    {
        $this->seed();
        View::share('categories', Category::all()->toArray());
        $productType_id = ProductType::first()->id;
        $user = User::first();
        $user->liked()->where('productType_id', $productType_id)->delete();
        $user->liked()->detach($productType_id);
        $liked_count = $user->liked()->count();

        $this->post('/api/products/liked/' . $productType_id)->assertUnauthorized();

        $this->withoutExceptionHandling();

        $this->actingAs($user)->get('/');
        $this->withHeader('Authorization', session('jwt'))->post('/api/products/liked/' . $productType_id)->assertOk();
        $this->assertTrue($user->liked()->count() == ($liked_count + 1));

        $this->withHeader('Authorization', session('jwt'))->post('/api/products/liked/' . $productType_id)->assertOk();
        $this->assertTrue($user->liked()->count() == $liked_count);
    }

    /**@test */
    public function test_a_comment_to_product_can_be_stored()
    {
        $this->seed();
        View::share('categories', Category::all()->toArray());
        $user = User::first();
        $user->ratingAndComments()->delete();
        $product_id = Product::first()->id;
        $data = ['product_id' => $product_id, 'rating' => 1];

        $this->post("/api/products/{$product_id}/comment", $data)->assertUnauthorized();

        $this->actingAs($user)->get('/');
        $this->withHeader('Authorization', session('jwt'))->post("/api/products/{$product_id}/comment", $data);
        $this->assertTrue($user->ratingAndComments()->count() == 1);
        $this->withHeader('Authorization', session('jwt'))->post("/api/products/{$product_id}/comment", $data)->assertInvalid();
        $user->ratingAndComments()->delete();

        $this->withoutExceptionHandling();

        $this->withHeader('Authorization', session('jwt'))->post("/api/products/{$product_id}/comment", $data)->assertOk();
        $this->assertTrue($user->ratingAndComments()->count() == 1);

        $product_id++;
        $data = ['product_id' => $product_id, 'rating' => 1, 'message' => '1'];
        $this->withHeader('Authorization', session('jwt'))->post("/api/products/{$product_id}/comment", $data)->assertOk();
        $this->assertTrue($user->ratingAndComments()->count() == 2);

        Storage::fake('public');
        $file = File::create('file.jpeg');
        $img = [[
            'path' => $file->getPathname(),
            'originalName' => $file->getClientOriginalName(),
            'mimeType' => $file->getClientMimeType(),
        ]];
        $product_id++;
        $data = ['product_id' => $product_id, 'rating' => 1, 'message' => '1', 'commentImages' => $img];
        $this->withHeader('Authorization', session('jwt'))->post("/api/products/{$product_id}/comment", $data)->assertOk();
        $this->assertTrue($user->ratingAndComments()->count() == 3);
        $file_path = CommentImage::latest('id')->first()->file_path;
        $this->assertTrue(Storage::disk('public')->exists($file_path));
    }
}

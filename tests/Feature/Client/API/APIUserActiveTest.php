<?php

namespace Client\API;

use App\Models\Category;
use App\Models\CommentImage;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class APIUserActiveTest extends TestCase
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
    public function test_a_product_can_be_liked()
    {
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

        $file = File::create('file.jpeg');
        $img = [
            'path' => $file->getPathname(),
            'originalName' => $file->getClientOriginalName(),
            'mimeType' => $file->getClientMimeType(),
        ];
        $product_id++;
        $data = ['product_id' => $product_id, 'rating' => 1, 'message' => '1', 'comment_images' => [$img]];
        $this->withHeader('Authorization', session('jwt'))->post("/api/products/{$product_id}/comment", $data)->assertOk();
        $this->assertTrue($user->ratingAndComments()->count() == 3);
        $file_path = CommentImage::latest('id')->first()->file_path;
        $this->assertTrue(Storage::exists($file_path));
    }
}

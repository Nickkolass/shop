<?php

namespace Client\API;

use App\Models\CommentImage;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class APIUserActiveTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_a_product_can_be_liked(): void
    {
        $productType_id = ProductType::query()->first()->id;
        $user = User::query()->first();
        $user->liked()->detach($productType_id);
        $route = route('back.api.products.likedToggle', $productType_id);
        $jwt = $this->getJwt($user);

        $this->post($route)->assertUnauthorized();

        $this->withoutExceptionHandling();

        $this->withHeader('Authorization', $jwt)->post($route)->assertOk();
        $this->assertTrue($user->liked()->where('productType_id', $productType_id)->exists());

        $this->withHeader('Authorization', $jwt)->post($route)->assertOk();
        $this->assertFalse($user->liked()->where('productType_id', $productType_id)->exists());
    }

    /**@test */
    public function test_a_comment_to_product_can_be_stored(): void
    {
        $user = User::query()->first();
        $user->ratingAndComments()->delete();
        $product_id = Product::query()->first()->id;
        $data = ['user_id' => $user->id, 'product_id' => $product_id, 'rating' => 1];
        $route = route('back.api.products.commentStore', $product_id);
        $jwt = $this->getJwt($user);

        $this->post($route, $data)->assertUnauthorized();

        $this->withHeader('Authorization', $jwt)->post($route, $data);
        $this->assertTrue($user->ratingAndComments()->where('product_id', $product_id)->exists());
        $this->withHeader('Authorization', $jwt)->post($route, $data)->assertInvalid();
        $user->ratingAndComments()->delete();

        $this->withoutExceptionHandling();

        $this->withHeader('Authorization', $jwt)->post($route, $data)->assertOk();
        $this->assertTrue($user->ratingAndComments()->where('product_id', $product_id)->exists());

        $product_id++;
        $route = route('back.api.products.commentStore', $product_id);
        $data = ['user_id' => $user->id, 'product_id' => $product_id, 'rating' => 1, 'message' => '1'];
        $this->withHeader('Authorization', $jwt)->post($route, $data)->assertOk();
        $this->assertTrue($user->ratingAndComments()->where('product_id', $product_id)->exists());

        $file = File::create('file.jpeg');
        $img = [
            'path' => $file->getPathname(),
            'originalName' => $file->getClientOriginalName(),
            'mimeType' => $file->getClientMimeType(),
        ];

        $product_id++;
        $route = route('back.api.products.commentStore', $product_id);
        $data = ['user_id' => $user->id, 'product_id' => $product_id, 'rating' => 1, 'message' => '1', 'comment_images' => [$img]];
        $this->withHeader('Authorization', $jwt)->post($route, $data)->assertOk();
        $this->assertTrue($user->ratingAndComments()->where('product_id', $product_id)->exists());
        $file_path = CommentImage::query()->latest('id')->first()->file_path;
        Storage::assertExists($file_path);
    }
}

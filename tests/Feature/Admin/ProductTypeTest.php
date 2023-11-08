<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Trait\StorageDbPrepareForTestTrait;
use Tests\TestCase;

class ProductTypeTest extends TestCase
{

    use StorageDbPrepareForTestTrait;

    /**@test */
    public function test_a_product_type_can_be_created_with_premissions(): void
    {
        $user = User::query()->has('products')->first();
        $product = $user->products()->first();
        $another_product = Product::query()->firstWhere('saler_id', '!=', $user->id);
        $route = route('admin.productTypes.create', $product->id);
        $another_route = route('admin.productTypes.create', $another_product->id);

        $this->get($another_route)->assertNotFound();

        $user->role = User::ROLE_CLIENT;
        $user->save();
        $this->actingAs($user)->get($another_route)->assertNotFound();
        session()->flush();

        $user->role = User::ROLE_SALER;
        $user->save();
        $this->actingAs($user)->get($another_route)->assertForbidden();
        session()->flush();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get($route)->assertBadRequest();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $product->productTypes()->take(1)->delete();
        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get($route)
                ->assertViewIs('admin.product.productType.create');
            session()->flush();
        }
        $another_product->productTypes()->take(1)->delete();
        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->get($another_route)
            ->assertViewIs('admin.product.productType.create');
        session()->flush();
    }

    /**@test */
    public function test_a_product_type_can_be_stored_with_premissions(): void
    {
        $user = User::query()->has('products')->first();
        $product = $user->products()->first();
        $another_product = Product::query()->firstWhere('saler_id', '!=', $user->id);
        $product->productTypes()->take(1)->delete();
        $another_product->productTypes()->take(1)->delete();
        $route = route('admin.productTypes.store', $product->id);
        $another_route = route('admin.productTypes.store', $another_product->id);

        $data = ProductType::factory()->raw();
        $data['preview_image'] = File::create('preview_image.jpeg');
        $data['relations'] = [
            'productImages' => [File::create('productImage.jpeg')],
            'optionValues' => $product->optionValues()->groupBy('option_id')->take(2)->pluck('optionValues.id')->all(),
        ];

        $this->post($another_route, $data)->assertNotFound();

        $user->role = User::ROLE_CLIENT;
        $user->save();
        $this->actingAs($user)->post($another_route, $data)->assertNotFound();
        session()->flush();

        $user->role = User::ROLE_SALER;
        $user->save();
        $this->actingAs($user)->post($another_route, $data)->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->post($route, $data)
                ->assertRedirectToRoute('admin.products.show', $product->id);
            session()->flush();
            $productType = ProductType::query()
                ->withCount('optionValues', 'productImages')
                ->firstWhere(['price' => $data['price'], 'count' => $data['count']]);
            $this->assertModelExists($productType);
            $this->assertCount($productType->option_values_count, $data['relations']['optionValues']);
            $this->assertCount($productType->product_images_count, $data['relations']['productImages']);
            $this->assertTrue(Storage::exists($productType->preview_image));
            $this->actingAs($user)->delete(route('admin.productTypes.destroy', $productType->id));
            $this->assertModelMissing($productType);
        }
        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->post($another_route, $data)
            ->assertRedirectToRoute('admin.products.show', $another_product->id);
        session()->flush();
        $productType = ProductType::query()
            ->withCount('optionValues', 'productImages')
            ->firstWhere(['price' => $data['price'], 'count' => $data['count']]);
        $this->assertModelExists($productType);
        $this->assertCount($productType->option_values_count, $data['relations']['optionValues']);
        $this->assertCount($productType->product_images_count, $data['relations']['productImages']);
        $this->assertTrue(Storage::exists($productType->preview_image));
    }

    /**@test */
    public function test_a_product_type_can_be_edited_with_premissions(): void
    {
        $user = User::query()->has('productTypes')->first();
        $productType = $user->productTypes()->first();
        $anotherProductType = ProductType::query()
            ->whereDoesntHave('product', fn($q) => $q->where('saler_id', $user->id))
            ->with('productImages')
            ->first();
        $route = route('admin.productTypes.edit', $productType->id);
        $another_route = route('admin.productTypes.edit', $anotherProductType->id);

        $this->get($another_route)->assertNotFound();

        $user->role = User::ROLE_CLIENT;
        $user->save();
        $this->actingAs($user)->get($another_route)->assertNotFound();
        session()->flush();

        $user->role = User::ROLE_SALER;
        $user->save();
        $this->actingAs($user)->get($another_route)->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get($route)
                ->assertViewIs('admin.product.productType.edit');
            session()->flush();
        }
        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->get($another_route)
            ->assertViewIs('admin.product.productType.edit');
        session()->flush();
    }

    /**@test */
    public function test_a_product_type_can_be_updated_with_premissions(): void
    {
        $user = User::query()->has('productTypes')->first();
        $productType = $user->productTypes()->with('productImages')->first();
        $anotherProductType = ProductType::query()
            ->whereDoesntHave('product', fn($q) => $q->where('saler_id', $user->id))
            ->with('productImages')
            ->first();
        $route = route('admin.productTypes.update', $productType->id);
        $another_route = route('admin.productTypes.update', $anotherProductType->id);

        $data = ProductType::factory()->raw();
        $data['preview_image'] = File::create('preview_image.jpeg');
        $data['relations'] = [
            'productImages' => [File::create('productImage.jpeg')],
            'optionValues' => $productType->product->optionValues()->take(2)->pluck('optionValues.id')->all(),
        ];

        $this->patch($another_route, $data)->assertNotFound();

        $user->role = User::ROLE_CLIENT;
        $user->save();
        $this->actingAs($user)->patch($another_route, $data)->assertNotFound();
        session()->flush();

        $user->role = User::ROLE_SALER;
        $user->save();
        $this->actingAs($user)->patch($another_route, $data)->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $data['preview_image'] = File::create('preview_image.jpeg');
            $data['relations']['productImages'] = [File::create('productImage.jpeg')];
            $this->actingAs($user)->patch($route, $data)
                ->assertRedirectToRoute('admin.products.show', $productType->product_id);
            session()->flush();
            $this->assertFalse(Storage::exists($productType->preview_image));
            $this->assertFalse(Storage::exists($productType->productImages->first()->file_path));
            $productType->refresh()->loadCount('optionValues', 'productImages');
            $this->assertTrue(Storage::exists($productType->preview_image));
            $this->assertTrue(Storage::exists($productType->productImages->first()->file_path));
            $this->assertEquals($data['price'], $productType->price);
            $this->assertCount($productType->option_values_count, $data['relations']['optionValues']);
            $this->assertCount($productType->product_images_count, $data['relations']['productImages']);
        }
        $user->role = User::ROLE_ADMIN;
        $user->save();
        $data['preview_image'] = File::create('preview_image.jpeg');
        $data['relations']['productImages'] = [File::create('productImage.jpeg')];

        Storage::delete($productType->preview_image);
        $this->actingAs($user)->patch($another_route, $data)
            ->assertRedirectToRoute('admin.products.show', $anotherProductType->product_id);
        session()->flush();
        $this->assertFalse(Storage::exists($anotherProductType->preview_image));
        $this->assertFalse(Storage::exists($anotherProductType->productImages->first()->file_path));
        $anotherProductType->refresh()->loadCount('optionValues', 'productImages');
        $this->assertTrue(Storage::exists($anotherProductType->preview_image));
        $this->assertTrue(Storage::exists($anotherProductType->productImages->first()->file_path));
        $this->assertEquals($anotherProductType->price, $data['price']);
        $this->assertCount($anotherProductType->option_values_count, $data['relations']['optionValues']);
        $this->assertCount($anotherProductType->product_images_count, $data['relations']['productImages']);
    }

    /**@test */
    public function test_a_product_type_can_be_deleted_with_premissions(): void
    {
        $user = User::query()->has('productTypes')->first();
        $anotherProductType = ProductType::query()
            ->whereDoesntHave('product', fn($q) => $q->where('saler_id', $user->id))
            ->with('productImages')
            ->first();
        $another_route = route('admin.productTypes.destroy', $anotherProductType->id);

        $this->delete($another_route)->assertNotFound();

        $user->role = User::ROLE_CLIENT;
        $user->save();
        $this->actingAs($user)->delete($another_route)->assertNotFound();
        session()->flush();

        $user->role = User::ROLE_SALER;
        $user->save();
        $this->actingAs($user)->delete($another_route)->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $productType = $user->productTypes()->with('productImages')->first();
            $this->actingAs($user)->delete(route('admin.productTypes.destroy', $productType->id))
                ->assertRedirectToRoute('admin.products.show', $productType->product_id);
            session()->flush();
            $this->assertFalse(Storage::exists($productType->preview_image));
            $this->assertFalse(Storage::exists($productType->productImages->first()->file_path));
            $this->assertModelMissing($productType);
            $this->assertFalse($productType->optionValues()->exists());
            $this->assertFalse($productType->productImages()->exists());
        }
        $user->role = User::ROLE_ADMIN;
        $user->save();
        $this->actingAs($user)->delete($another_route)
            ->assertRedirectToRoute('admin.products.show', $anotherProductType->product_id);
        session()->flush();
        $this->assertFalse(Storage::exists($anotherProductType->preview_image));
        $this->assertFalse(Storage::exists($anotherProductType->productImages->first()->file_path));
        $this->assertModelMissing($anotherProductType);
        $this->assertFalse($anotherProductType->optionValues()->exists());
        $this->assertFalse($anotherProductType->productImages()->exists());
    }
}

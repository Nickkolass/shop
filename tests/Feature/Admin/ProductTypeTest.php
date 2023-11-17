<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class ProductTypeTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_a_product_type_can_be_created_with_premissions(): void
    {
        $user = User::query()->has('products')->first();
        $product = $user->products()->first();
        $another_product = Product::query()->firstWhere('saler_id', '!=', $user->id);
        $route = route('admin.productTypes.create', $product->id);
        $another_route = route('admin.productTypes.create', $another_product->id);

        $this->get($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->get($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_SALER]);
        $user->update(['role' => User::ROLE_SALER]);
        $this->actingAs($user)->get($another_route)->assertForbidden();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertBadRequest();
        }

        $this->withoutExceptionHandling();

        $product->productTypes()->take(1)->delete();
        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)
                ->assertViewIs('admin.product.productType.create');
        }
        $another_product->productTypes()->take(1)->delete();
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($another_route)
            ->assertViewIs('admin.product.productType.create');
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

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->post($another_route, $data)->assertNotFound();

        session(['user.role' => User::ROLE_SALER]);
        $user->update(['role' => User::ROLE_SALER]);
        $this->actingAs($user)->post($another_route, $data)->assertForbidden();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->post($route, $data)
                ->assertRedirectToRoute('admin.products.show', $product->id);
            $productType = ProductType::query()
                ->withCount('optionValues', 'productImages')
                ->firstWhere(['price' => $data['price'], 'count' => $data['count']]);
            $this->assertModelExists($productType);
            $this->assertCount($productType->option_values_count, $data['relations']['optionValues']);
            $this->assertCount($productType->product_images_count, $data['relations']['productImages']);
            Storage::assertExists($productType->preview_image);
            $this->actingAs($user)->delete(route('admin.productTypes.destroy', $productType->id));
            $this->assertModelMissing($productType);
        }
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->post($another_route, $data)
            ->assertRedirectToRoute('admin.products.show', $another_product->id);
        $productType = ProductType::query()
            ->withCount('optionValues', 'productImages')
            ->firstWhere(['price' => $data['price'], 'count' => $data['count']]);
        $this->assertModelExists($productType);
        $this->assertCount($productType->option_values_count, $data['relations']['optionValues']);
        $this->assertCount($productType->product_images_count, $data['relations']['productImages']);
        Storage::assertExists($productType->preview_image);
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

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->get($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_SALER]);
        $user->update(['role' => User::ROLE_SALER]);
        $this->actingAs($user)->get($another_route)->assertForbidden();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)
                ->assertViewIs('admin.product.productType.edit');
        }
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($another_route)
            ->assertViewIs('admin.product.productType.edit');
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

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->patch($another_route, $data)->assertNotFound();

        session(['user.role' => User::ROLE_SALER]);
        $user->update(['role' => User::ROLE_SALER]);
        $this->actingAs($user)->patch($another_route, $data)->assertForbidden();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $data['preview_image'] = File::create('preview_image.jpeg');
            $data['relations']['productImages'] = [File::create('productImage.jpeg')];
            $this->actingAs($user)->patch($route, $data)
                ->assertRedirectToRoute('admin.products.show', $productType->product_id);
            Storage::assertMissing($productType->preview_image);
            Storage::assertMissing($productType->productImages->first()->file_path);
            $productType->refresh()->loadCount('optionValues', 'productImages');
            Storage::assertExists($productType->preview_image);
            Storage::assertExists($productType->productImages->first()->file_path);
            $this->assertEquals($data['price'], $productType->price);
            $this->assertCount($productType->option_values_count, $data['relations']['optionValues']);
            $this->assertCount($productType->product_images_count, $data['relations']['productImages']);
        }
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $data['preview_image'] = File::create('preview_image.jpeg');
        $data['relations']['productImages'] = [File::create('productImage.jpeg')];

        Storage::delete($productType->preview_image);
        $this->actingAs($user)->patch($another_route, $data)
            ->assertRedirectToRoute('admin.products.show', $anotherProductType->product_id);
        Storage::assertMissing($anotherProductType->preview_image);
        Storage::assertMissing($anotherProductType->productImages->first()->file_path);
        $anotherProductType->refresh()->loadCount('optionValues', 'productImages');
        Storage::assertExists($anotherProductType->preview_image);
        Storage::assertExists($anotherProductType->productImages->first()->file_path);
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

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->delete($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_SALER]);
        $user->update(['role' => User::ROLE_SALER]);
        $this->actingAs($user)->delete($another_route)->assertForbidden();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $productType = $user->productTypes()->with('productImages')->first();
            $this->actingAs($user)->delete(route('admin.productTypes.destroy', $productType->id))
                ->assertRedirectToRoute('admin.products.show', $productType->product_id);
            Storage::assertMissing($productType->preview_image);
            Storage::assertMissing($productType->productImages->first()->file_path);
            $this->assertModelMissing($productType);
            $this->assertFalse($productType->optionValues()->exists());
            $this->assertFalse($productType->productImages()->exists());
        }
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->delete($another_route)
            ->assertRedirectToRoute('admin.products.show', $anotherProductType->product_id);
        Storage::assertMissing($anotherProductType->preview_image);
        Storage::assertMissing($anotherProductType->productImages->first()->file_path);
        $this->assertModelMissing($anotherProductType);
        $this->assertFalse($anotherProductType->optionValues()->exists());
        $this->assertFalse($anotherProductType->productImages()->exists());
    }
}

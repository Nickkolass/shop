<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTypeTest extends TestCase
{

    use RefreshDatabase;

    /**@test */
    public function test_a_product_type_can_be_created_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $product = $user->products()->first();
        $another_product = Product::where('saler_id', '!=', $user->id)->first();

        $this->get(route('admin.productTypes.create', $another_product->id))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.productTypes.create', $another_product->id))->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->get(route('admin.productTypes.create', $another_product->id))->assertForbidden();
        session()->flush();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.productTypes.create', $product->id))->assertBadRequest();
            session()->flush();
        }

        $this->withoutExceptionHandling();

        $product->productTypes()->take(1)->delete();
        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.productTypes.create', $product->id))
                ->assertViewIs('admin.product.productType.create');
            session()->flush();
        }
        $another_product->productTypes()->take(1)->delete();
        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.productTypes.create', $another_product->id))
            ->assertViewIs('admin.product.productType.create');
        session()->flush();
    }

    /**@test */
    public function test_a_product_type_can_be_stored_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $product = $user->products()->first();
        $another_product = Product::where('saler_id', '!=', $user->id)->first();

        Storage::fake('public');

        $data = ProductType::factory()->raw();
        $data['preview_image'] = File::create('preview_image.jpeg');
        $data['productImages'] = [File::create('productImage.jpeg')];
        $data['optionValues'] = $product->optionValues()->take(2)->pluck('optionValues.id')->all();
        $data['product_id'] = $another_product->id;

        $this->post(route('admin.productTypes.store', $another_product->id), $data)->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->post(route('admin.productTypes.store', $another_product->id), $data)->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->post(route('admin.productTypes.store', $another_product->id), $data)->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        $data['product_id'] = $product->id;
        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $product->productTypes()->take(1)->delete();
            $this->actingAs($user)->post(route('admin.productTypes.store', $product->id), $data)
                ->assertRedirect(route('admin.products.show', $product->id));
            session()->flush();
            $productType = ProductType::latest('id')->first();
            $this->assertEquals($productType->price, $data['price']);
            $this->assertCount($productType->optionValues()->count(), $data['optionValues']);
            $this->assertCount($productType->productImages()->count(), $data['productImages']);
            $this->assertTrue(Storage::disk('public')->exists($productType->preview_image));
        }
        $user->role = 1;
        $user->save();
        $data['product_id'] = $another_product->id;
        $another_product->productTypes()->take(1)->delete();
        $this->actingAs($user)->post(route('admin.productTypes.store', $another_product->id), $data)
            ->assertRedirect(route('admin.products.show', $another_product->id));
        session()->flush();
        $productType = ProductType::latest('id')->first();
        $this->assertEquals($productType->price, $data['price']);
        $this->assertCount($productType->optionValues()->count(), $data['optionValues']);
        $this->assertCount($productType->productImages()->count(), $data['productImages']);
        $this->assertTrue(Storage::disk('public')->exists($productType->preview_image));
    }

    /**@test */
    public function test_a_product_type_can_be_edited_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $productType = $user->productTypes()->first();
        $anotherProductType = Product::where('saler_id', '!=', $user->id)->first()->productTypes()->first();

        $this->get(route('admin.productTypes.edit', $anotherProductType->id))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.productTypes.edit', $anotherProductType->id))->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->get(route('admin.productTypes.edit', $anotherProductType->id))->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.productTypes.edit', $productType->id))
                ->assertViewIs('admin.product.productType.edit');
            session()->flush();
        }
        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.productTypes.edit', $anotherProductType->id))
            ->assertViewIs('admin.product.productType.edit');
        session()->flush();
    }

    /**@test */
    public function test_a_product_type_can_be_updated_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $productType = $user->productTypes()->with('productImages')->first();
        $anotherProductType = Product::where('saler_id', '!=', $user->id)->first()->productTypes()->with('productImages')->first();

        Storage::fake('public');

        $data = ProductType::factory()->raw();
        $data['preview_image'] = File::create('preview_image.jpeg');
        $data['productImages'] = [File::create('productImage.jpeg')];
        $data['optionValues'] = $productType->product->optionValues()->take(2)->pluck('optionValues.id')->all();
        $data['product_id'] = $anotherProductType->product_id;

        $this->patch(route('admin.productTypes.update', $anotherProductType->id), $data)->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->patch(route('admin.productTypes.update', $anotherProductType->id), $data)->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->patch(route('admin.productTypes.update', $anotherProductType->id), $data)->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        $data['product_id'] = $productType->product_id;
        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $data['preview_image'] = File::create('preview_image.jpeg');
            $data['productImages'] = [File::create('productImage.jpeg')];
            $productType->refresh();
            $this->actingAs($user)->patch(route('admin.productTypes.update', $productType->id), $data)
                ->assertRedirect(route('admin.products.show', $productType->product_id));
            session()->flush();
            $this->assertFalse(Storage::disk('public')->exists($productType->preview_image));
            $this->assertFalse(Storage::disk('public')->exists($productType->productImages->first()->file_path));
            $productType->refresh();
            $this->assertTrue(Storage::disk('public')->exists($productType->preview_image));
            $this->assertTrue(Storage::disk('public')->exists($productType->productImages->first()->file_path));
            $this->assertEquals($data['price'], $productType->price);
            $this->assertCount($productType->optionValues()->count(), $data['optionValues']);
            $this->assertCount($productType->productImages()->count(), $data['productImages']);
        }
        $user->role = 1;
        $user->save();
        $data['product_id'] = $anotherProductType->product_id;
        $data['preview_image'] = File::create('preview_image.jpeg');
        $data['productImages'] = [File::create('productImage.jpeg')];

        Storage::disk('public')->delete($productType->preview_image);
        $this->actingAs($user)->patch(route('admin.productTypes.update', $anotherProductType->id), $data)
            ->assertRedirect(route('admin.products.show', $anotherProductType->product_id));
        session()->flush();
        $this->assertFalse(Storage::disk('public')->exists($anotherProductType->preview_image));
        $this->assertFalse(Storage::disk('public')->exists($anotherProductType->productImages->first()->file_path));
        $anotherProductType->refresh();
        $this->assertTrue(Storage::disk('public')->exists($anotherProductType->preview_image));
        $this->assertTrue(Storage::disk('public')->exists($anotherProductType->productImages->first()->file_path));
        $this->assertEquals($anotherProductType->price, $data['price']);
        $this->assertCount($anotherProductType->optionValues()->count(), $data['optionValues']);
        $this->assertCount($anotherProductType->productImages()->count(), $data['productImages']);
    }

    /**@test */
    public function test_a_product_type_can_be_deleted_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $anotherProductType = Product::where('saler_id', '!=', $user->id)->first()->productTypes()->with('productImages')->first();
        Storage::fake('public');

        $this->delete(route('admin.productTypes.destroy', $anotherProductType->id))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->delete(route('admin.productTypes.destroy', $anotherProductType->id))->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->delete(route('admin.productTypes.destroy', $anotherProductType->id))->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $productType = $user->productTypes()->with('productImages')->first();
            $this->actingAs($user)->delete(route('admin.productTypes.destroy', $productType->id))
                ->assertRedirect(route('admin.products.show', $productType->product_id));
            session()->flush();
            $this->assertFalse(Storage::disk('public')->exists($productType->preview_image));
            $this->assertFalse(Storage::disk('public')->exists($productType->productImages->first()->file_path));
            $this->assertEmpty(ProductType::find($productType->id));
            $this->assertEmpty($productType->optionValues()->count());
            $this->assertEmpty($productType->productImages()->count());
        }
        $user->role = 1;
        $user->save();
        $this->actingAs($user)->delete(route('admin.productTypes.destroy', $anotherProductType->id))
            ->assertRedirect(route('admin.products.show', $anotherProductType->product_id));
        session()->flush();
        $this->assertFalse(Storage::disk('public')->exists($anotherProductType->preview_image));
        $this->assertFalse(Storage::disk('public')->exists($anotherProductType->productImages->first()->file_path));
        $this->assertEmpty(ProductType::find($anotherProductType->id));
        $this->assertEmpty($anotherProductType->optionValues()->count());
        $this->assertEmpty($anotherProductType->productImages()->count());
    }
}
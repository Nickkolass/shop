<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\OptionValue;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\PropertyValue;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductTest extends TestCase
{

    use RefreshDatabase;

    /**@test */
    public function test_a_product_can_be_viewed_any_with_premissions()
    {
        $this->seed();
        $user = User::first();

        $this->get(route('admin.products.index'))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.index'))->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.products.index'))->assertViewIs('admin.product.index');
            session()->flush();
        }
    }

    /**@test */
    public function test_a_product_can_be_viewed_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $product = $user->products()->first();
        $another_product = Product::where('saler_id', '!=', $user->id)->first();

        $this->get(route('admin.products.show', $another_product->id))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.show', $another_product->id))->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.products.show', $product->id))->assertViewIs('admin.product.show');
            session()->flush();
        }
        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.show', $another_product->id))->assertViewIs('admin.product.show');
        session()->flush();
    }

    /**@test */
    public function test_a_product_can_be_create_index_with_premissions()
    {
        $this->seed();
        $user = User::first();

        $this->get(route('admin.products.create'))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.create'))->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.products.create'))->assertViewIs('admin.product.create.index_create');
            session()->flush();
        }
    }

    /**@test */
    public function test_a_product_can_be_create_properties_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $data = [
            'title' => '1',
            'description' => '1',
            'category_id' => Category::first()->id,
            'saler_id' => $user->id,
            'tags' => Tag::take(3)->pluck('id')->all(),
        ];

        $this->post(route('admin.products.createProperties'), $data)->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->post(route('admin.products.createProperties'), $data)->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $data['title'] = Str::random(5);
            $this->actingAs($user)->post(route('admin.products.createProperties'), $data)->assertViewIs('admin.product.create.properties_create');
            session()->flush();
        }
    }

    /**@test */
    public function test_a_product_can_be_create_types_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $data['propertyValues'] = PropertyValue::groupBy('property_id')->take(2)->pluck('value', 'property_id')->all();
        $data['optionValues'] = OptionValue::query()->take(2)->select('option_id', 'id')->get()
            ->groupBy('option_id')->map(fn($optionValue) => $optionValue->pluck('id'))->toArray();
        $data = http_build_query($data);

        $this->get(route('admin.products.createTypes') . '?' . $data)->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.createTypes') . '?' . $data)->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->from(route('admin.products.createProperties'))
                ->get(route('admin.products.createTypes') . '?' . $data)
                ->assertViewIs('admin.product.create.types_create');
            session()->flush();
        }
    }

    /**@test */
    public function test_a_product_can_be_stored_with_premissions()
    {
        $this->seed();
        $user = User::first();
        Storage::fake('public');

        $session = [
            'title' => '1',
            'description' => '1',
            'category_id' => Category::first()->id,
            'saler_id' => $user->id,
            'tags' => Tag::take(3)->pluck('id')->all(),
            'propertyValues' => PropertyValue::groupBy('property_id')->take(2)->pluck('value', 'property_id')->all(),
        ];

        $data['types'] = [[
            'price' => 1,
            'count' => 1,
            'is_published' => false,
            'preview_image' => File::create('preview_image.jpeg'),
            'productImages' => [File::create('productImage.jpeg')],
            'optionValues' => [OptionValue::first()->id],
        ], [
            'price' => 2,
            'count' => 2,
            'is_published' => false,
            'preview_image' => File::create('preview_image.jpeg'),
            'productImages' => [File::create('productImage.jpeg')],
            'optionValues' => [OptionValue::find(OptionValue::first()->id + 1)->id],
        ]];

        session(['create' => $session]);
        $this->post(route('admin.products.store'), $data)->assertNotFound();
        session()->flush();

        $user->role = 3;
        $user->save();
        session(['create' => $session]);
        $this->actingAs($user)->post(route('admin.products.store'), $data)->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        $session['saler_id'] = $user->id;
        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $session['title'] = Str::random(5);
            session(['create' => $session]);
            $res = $this->actingAs($user)->post(route('admin.products.store'), $data);
            $product = Product::with(['productTypes', 'tags'])->latest('id')->first();
            $res->assertRedirect(route('admin.products.show', $product->id));
            $this->assertEquals($session['title'], $product->title);
            $this->assertTrue($product->productTypes->count() == 2);
            $this->assertTrue($product->tags->count() == 3);

            $file_path = ProductImage::latest('id')->first()->file_path;
            $this->assertTrue(Storage::disk('public')->exists($file_path));
            $this->assertTrue(Storage::disk('public')->exists($product->productTypes->first()->preview_image));

            $this->actingAs($user)->delete(route('admin.products.destroy', $product->id));
            $this->assertFalse(Product::where('id', $product->id)->exists());
            session()->flush();
        }
    }

    /**@test */
    public function test_a_product_can_be_edit_index_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $product = $user->products()->first();
        $another_product = Product::where('saler_id', '!=', $user->id)->first();

        $this->get(route('admin.products.edit', $another_product->id))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.edit', $another_product->id))->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.edit', $another_product->id))->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.products.edit', $product->id))
                ->assertViewIs('admin.product.edit.index_edit');
            session()->flush();
        }

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.edit', $another_product->id))
            ->assertViewIs('admin.product.edit.index_edit');
    }

    /**@test */
    public function test_a_product_can_be_edit_properties_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $product = $user->products()->first();
        $data = [
            'title' => '1',
            'description' => '1',
            'category_id' => Category::first()->id,
            'saler_id' => $user->id,
            'tags' => Tag::take(3)->pluck('id')->all(),
        ];

        $this->post(route('admin.products.editProperties', $product->id), $data)->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->post(route('admin.products.editProperties', $product->id), $data)->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->post(route('admin.products.editProperties', $product->id), $data)
                ->assertViewIs('admin.product.edit.properties_edit');
            session()->flush();
        }
    }

    /**@test */
    public function test_a_product_can_be_updated_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $product = $user->products()->first();
        $another_product = Product::where('saler_id', '!=', $user->id)->first();

        $session = [
            'title' => '1',
            'description' => '1',
            'category_id' => Category::first()->id,
            'saler_id' => $user->id,
            'tags' => Tag::take(3)->pluck('id')->all(),
        ];

        $data['propertyValues'] = PropertyValue::groupBy('property_id')->take(2)->pluck('value', 'property_id')->all();
        $data['optionValues'] = OptionValue::query()->take(2)->select('option_id', 'id')->get()
            ->groupBy('option_id')->map(fn($optionValue) => $optionValue->pluck('id'))->toArray();

        session(['edit' => $session]);
        $this->patch(route('admin.products.update', $another_product->id), $data)->assertNotFound();
        session()->flush();

        $user->role = 3;
        $user->save();
        session(['edit' => $session]);
        $this->actingAs($user)->patch(route('admin.products.update', $another_product->id), $data)->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        session(['edit' => $session]);
        $this->actingAs($user)->patch(route('admin.products.update', $another_product->id), $data)->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $data['propertyValues'] = PropertyValue::groupBy('property_id')->take(2)->pluck('value', 'property_id')->all();
            $data['optionValues'] = OptionValue::query()->take(2)->select('option_id', 'id')->get()
                ->groupBy('option_id')->map(fn($optionValue) => $optionValue->pluck('id'))->toArray();
            $session['title'] = Str::random(5);
            session(['edit' => $session]);
            $this->actingAs($user)->patch(route('admin.products.update', $product->id), $data)
                ->assertRedirect(route('admin.products.show', $product->id));
            $product->refresh();
            $this->assertEquals($session['title'], $product->title);
            $this->assertEquals($product->propertyValues()->pluck('value', 'property_id')->all(), $data['propertyValues']);
            $this->assertTrue($product->optionValues()->pluck('optionValues.id') == collect($data['optionValues'])->flatten());
            session()->flush();
        }
        $user->role = 1;
        $user->save();
        $data['propertyValues'] = PropertyValue::groupBy('property_id')->take(2)->pluck('value', 'property_id')->all();
        $data['optionValues'] = OptionValue::query()->take(2)->select('option_id', 'id')->get()
            ->groupBy('option_id')->map(fn($optionValue) => $optionValue->pluck('id'))->toArray();
        $session['title'] = Str::random(5);
        session(['edit' => $session]);
        $this->actingAs($user)->patch(route('admin.products.update', $another_product->id), $data)
            ->assertRedirect(route('admin.products.show', $another_product->id));
        $another_product->refresh();
        $this->assertEquals($session['title'], $another_product->title);
        $this->assertEquals($another_product->propertyValues()->pluck('value', 'property_id')->all(), $data['propertyValues']);
        $this->assertTrue($another_product->optionValues()->pluck('optionValues.id') == collect($data['optionValues'])->flatten());
        session()->flush();
    }

    /**@test */
    public function test_a_product_can_be_deleted_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $another_product = Product::where('saler_id', '!=', $user->id)->with('productTypes.productImages')->first();
        Storage::fake('public');

        $this->delete(route('admin.products.destroy', $another_product->id))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->delete(route('admin.products.destroy', $another_product->id))->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->delete(route('admin.products.destroy', $another_product->id))->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $product = $user->products()->with('productTypes.productImages')->first();
            $this->actingAs($user)->delete(route('admin.products.destroy', $product->id))->assertViewIs('admin.product.index');
            session()->flush();
            $this->assertEmpty($product->optionValues()->count());
            $this->assertEmpty($product->propertyValues()->count());
            $this->assertEmpty($product->tags()->count());
            $this->assertEmpty($product->productTypes()->count());
            $productType_ids = $product->productTypes()->pluck('id');
            $this->assertEmpty(ProductImage::whereIn('productType_id', $productType_ids)->count());
            $this->assertEmpty(OptionValue::whereIn('productType_id', $productType_ids)->count());
            $this->assertEmpty(Product::find($product->id));

            $this->assertFalse(Storage::disk('public')->exists($product->productTypes->first()->productImages->first()->file_path));
            $this->assertFalse(Storage::disk('public')->exists($product->productTypes->first()->preview_image));
        }
        $user->role = 1;
        $user->save();
        $this->actingAs($user)->delete(route('admin.products.destroy', $another_product->id))->assertViewIs('admin.product.index');
        session()->flush();
        $this->assertEquals($another_product->optionValues()->count(), 0);
        $this->assertEquals($another_product->propertyValues()->count(), 0);
        $this->assertEquals($another_product->productTypes()->count(), 0);
        $this->assertEquals($another_product->tags()->count(), 0);
        $productType_ids = $another_product->productTypes()->pluck('id');
        $this->assertEquals(ProductImage::whereIn('productType_id', $productType_ids)->count(), 0);
        $this->assertEquals(OptionValue::whereIn('productType_id', $productType_ids)->count(), 0);
        $this->assertEmpty(Product::find($another_product->id));

        $this->assertFalse(Storage::disk('public')->exists($another_product->productTypes->first()->productImages->first()->file_path));
        $this->assertFalse(Storage::disk('public')->exists($another_product->productTypes->first()->preview_image));
    }

    /**@test */
    public function test_a_product_can_be_published_with_premissions()
    {
        $this->seed();
        $user = User::first();
        $product = $user->products()->first();
        $another_product = Product::where('saler_id', '!=', $user->id)->first();

        $this->patch(route('admin.products.publish', $another_product->id))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->patch(route('admin.products.publish', $another_product->id))->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        $this->actingAs($user)->patch(route('admin.products.publish', $another_product->id))->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $product->productTypes()->update(['is_published' => 1]);
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->patch(route('admin.products.publish', $product->id))->assertRedirect();
            $this->assertEmpty($product->productTypes()->where('is_published', 1)->count());
            session()->flush();
            $this->actingAs($user)->patch(route('admin.products.publish', $product->id), ['publish' => 'on'])->assertRedirect();
            $this->assertEmpty($product->productTypes()->where('is_published', 0)->count());
            session()->flush();
        }
        $product->productTypes()->update(['is_published' => 1]);
        $user->role = 1;
        $user->save();
        $this->actingAs($user)->patch(route('admin.products.publish', $another_product->id))->assertRedirect();
        $this->assertEmpty($another_product->productTypes()->where('is_published', 1)->count());
        session()->flush();
        $this->actingAs($user)->patch(route('admin.products.publish', $another_product->id), ['publish' => 'on'])->assertRedirect();
        $this->assertEmpty($another_product->productTypes()->where('is_published', 0)->count());
        session()->flush();
    }
}

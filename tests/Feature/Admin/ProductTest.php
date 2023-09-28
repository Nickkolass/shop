<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\OptionValue;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\PropertyValue;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    protected function tearDown(): void
    {
        foreach (Storage::directories() as $dir) if ($dir != 'factory') Storage::deleteDirectory($dir);
        parent::tearDown();
    }

    /**@test */
    public function test_a_product_can_be_viewed_any_with_premissions(): void
    {
        $user = User::query()->first();

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
    public function test_a_product_can_be_viewed_with_premissions(): void
    {
        $user = User::query()->first();
        $product = $user->products()->first();
        $another_product = Product::query()->where('saler_id', '!=', $user->id)->first();

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
    public function test_a_product_can_be_create_index_with_premissions(): void
    {
        $user = User::query()->first();

        $this->get(route('admin.products.create'))->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.create'))->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.products.create'))->assertViewIs('admin.product.create.index');
            session()->flush();
        }
    }

    /**@test */
    public function test_a_product_can_be_create_relations_with_premissions(): void
    {
        $user = User::query()->first();
        $data = [
            'title' => '1',
            'description' => '1',
            'category_id' => Category::query()->first()->id,
            'saler_id' => $user->id,
        ];
        $data = http_build_query($data);

        $this->get(route('admin.products.create.relations') . '?' . $data)->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.create.relations') . '?' . $data)->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.products.create.relations') . '?' . $data)->assertViewIs('admin.product.create.relations');
            session()->flush();
        }
    }

    /**@test */
    public function test_a_product_can_be_create_types_with_premissions(): void
    {
        $user = User::query()->first();
        $data['tags'] = Tag::query()->take(3)->pluck('id')->all();
        $data['propertyValues'] = PropertyValue::query()->groupBy('property_id')->take(2)->pluck('value', 'property_id')->all();
        $data['optionValues'] = OptionValue::query()->take(2)->select('option_id', 'id')->get()
            ->groupBy('option_id')->map(fn($optionValue) => $optionValue->pluck('id'))->toArray();
        $data = http_build_query($data);

        $this->get(route('admin.products.create.types') . '?' . $data)->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.create.types') . '?' . $data)->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->from(route('admin.products.create.relations'))
                ->get(route('admin.products.create.types') . '?' . $data)
                ->assertViewIs('admin.product.create.types');
            session()->flush();
        }
    }

    /**@test */
    public function test_a_product_can_be_stored_with_premissions(): void
    {
        $user = User::query()->first();

        $session = ['create' => [
            'product' => [
                'title' => '1',
                'description' => '1',
                'category_id' => Category::query()->first()->id,
                'saler_id' => $user->id,
            ],
            'relations' => [
                'tags' => Tag::query()->take(3)->pluck('id')->all(),
                'propertyValues' => PropertyValue::query()->groupBy('property_id')->take(2)->pluck('value', 'property_id')->all(),
                'optionValues' => OptionValue::query()->take(2)->pluck('id')->all(),
            ],
        ]];

        $data['types'] = [[
            'price' => 1,
            'count' => 1,
            'is_published' => false,
            'preview_image' => File::create('preview_image.jpeg'),
            'relations' => [
                'productImages' => [File::create('productImage.jpeg')],
                'optionValues' => [OptionValue::query()->first()->id],
            ]], [
            'price' => 2,
            'count' => 2,
            'is_published' => false,
            'preview_image' => File::create('preview_image.jpeg'),
            'relations' => [
                'productImages' => [File::create('productImage.jpeg')],
                'optionValues' => [OptionValue::query()->inRandomOrder()->first()->id],
            ]],
        ];

        session($session);
        $this->post(route('admin.products.store'), $data)->assertNotFound();
        session()->flush();

        $user->role = 3;
        $user->save();
        session($session);
        $this->actingAs($user)->post(route('admin.products.store'), $data)->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        $session['create']['product']['saler_id'] = $user->id;
        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $session['create']['product']['title'] = Str::random(5);
            session($session);
            $res = $this->actingAs($user)->post(route('admin.products.store'), $data);
            $product = Product::query()->with(['productTypes', 'tags'])->latest('id')->first();

            $res->assertRedirect(route('admin.products.show', $product->id));
            $this->assertEquals($session['create']['product']['title'], $product->title);
            $this->assertTrue($product->productTypes->count() == 2);
            $this->assertTrue($product->tags->count() == 3);

            $file_path = ProductImage::query()->latest('id')->first()->file_path;
            $this->assertTrue(Storage::exists($file_path));
            $this->assertTrue(Storage::exists($product->productTypes->first()->preview_image));

            $this->actingAs($user)->delete(route('admin.products.destroy', $product->id));
            $this->assertFalse(Product::query()->where('id', $product->id)->exists());
            session()->flush();
        }
    }

    /**@test */
    public function test_a_product_can_be_edit_index_with_premissions(): void
    {
        $user = User::query()->first();
        $product = $user->products()->first();
        $another_product = Product::query()->where('saler_id', '!=', $user->id)->first();

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
                ->assertViewIs('admin.product.edit.index');
            session()->flush();
        }

        $user->role = 1;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.edit', $another_product->id))
            ->assertViewIs('admin.product.edit.index');
    }

    /**@test */
    public function test_a_product_can_be_edit_relations_with_premissions(): void
    {
        $user = User::query()->first();
        $product = $user->products()->first();
        $data = [
            'title' => '1',
            'description' => '1',
            'category_id' => Category::query()->first()->id,
            'saler_id' => $user->id,
        ];
        $data = http_build_query($data);

        $this->get(route('admin.products.edit.relations', $product->id) . '?' . $data)->assertNotFound();

        $user->role = 3;
        $user->save();
        $this->actingAs($user)->get(route('admin.products.edit.relations', $product->id) . '?' . $data)->assertNotFound();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $this->actingAs($user)->get(route('admin.products.edit.relations', $product->id) . '?' . $data)
                ->assertViewIs('admin.product.edit.relations');
            session()->flush();
        }
    }

    /**@test */
    public function test_a_product_can_be_updated_with_premissions(): void
    {
        $user = User::query()->first();
        $product = $user->products()->first();
        $another_product = Product::query()->where('saler_id', '!=', $user->id)->first();

        $session = ['edit' => [
            'title' => '1',
            'description' => '1',
            'category_id' => Category::query()->first()->id,
            'saler_id' => $user->id,
        ]];
        $data['tags'] = Tag::query()->take(3)->pluck('id')->all();
        $data['propertyValues'] = PropertyValue::query()->groupBy('property_id')->take(2)->pluck('value', 'property_id')->all();
        $data['optionValues'] = OptionValue::query()->take(2)->select('option_id', 'id')->get()
            ->groupBy('option_id')->map(fn($optionValue) => $optionValue->pluck('id'))->toArray();

        session($session);
        $this->patch(route('admin.products.update', $another_product->id), $data)->assertNotFound();
        session()->flush();

        $user->role = 3;
        $user->save();
        session($session);
        $this->actingAs($user)->patch(route('admin.products.update', $another_product->id), $data)->assertNotFound();
        session()->flush();

        $user->role = 2;
        $user->save();
        session($session);
        $this->actingAs($user)->patch(route('admin.products.update', $another_product->id), $data)->assertForbidden();
        session()->flush();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->role = $i;
            $user->save();
            $data['tags'] = Tag::query()->take(3)->pluck('id')->all();
            $data['propertyValues'] = PropertyValue::query()->groupBy('property_id')->take(2)->pluck('value', 'property_id')->all();
            $data['optionValues'] = OptionValue::query()->take(2)->select('option_id', 'id')->get()
                ->groupBy('option_id')->map(fn($optionValue) => $optionValue->pluck('id'))->toArray();
            $session['edit']['title'] = Str::random(5);
            session($session);
            $this->actingAs($user)->patch(route('admin.products.update', $product->id), $data)
                ->assertRedirect(route('admin.products.show', $product->id));
            $product->refresh();
            $this->assertEquals($session['edit']['title'], $product->title);
            $this->assertEquals($product->propertyValues()->pluck('value', 'property_id')->all(), $data['propertyValues']);
            $this->assertTrue($product->optionValues()->pluck('optionValues.id') == collect($data['optionValues'])->flatten());
            session()->flush();
        }
        $user->role = 1;
        $user->save();
        $data['tags'] = Tag::query()->take(3)->pluck('id')->all();
        $data['propertyValues'] = PropertyValue::query()->groupBy('property_id')->take(2)->pluck('value', 'property_id')->all();
        $data['optionValues'] = OptionValue::query()->take(2)->select('option_id', 'id')->get()
            ->groupBy('option_id')->map(fn($optionValue) => $optionValue->pluck('id'))->toArray();
        $session['edit']['title'] = Str::random(5);
        session($session);
        $this->actingAs($user)->patch(route('admin.products.update', $another_product->id), $data)
            ->assertRedirect(route('admin.products.show', $another_product->id));
        $another_product->refresh();
        $this->assertEquals($session['edit']['title'], $another_product->title);
        $this->assertEquals($another_product->propertyValues()->pluck('value', 'property_id')->all(), $data['propertyValues']);
        $this->assertTrue($another_product->optionValues()->pluck('optionValues.id') == collect($data['optionValues'])->flatten());
        session()->flush();
    }

    /**@test */
    public function test_a_product_can_be_deleted_with_premissions(): void
    {
        $user = User::query()->first();
        $another_product = Product::query()->where('saler_id', '!=', $user->id)->with('productTypes.productImages')->first();

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
            $this->assertEmpty(ProductImage::query()->whereIn('productType_id', $productType_ids)->count());
            $this->assertEmpty(OptionValue::query()->whereIn('productType_id', $productType_ids)->count());
            $this->assertEmpty(Product::query()->find($product->id));

            $this->assertFalse(Storage::exists($product->productTypes->first()->productImages->first()->file_path));
            $this->assertFalse(Storage::exists($product->productTypes->first()->preview_image));
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
        $this->assertEquals(ProductImage::query()->whereIn('productType_id', $productType_ids)->count(), 0);
        $this->assertEquals(OptionValue::query()->whereIn('productType_id', $productType_ids)->count(), 0);
        $this->assertEmpty(Product::query()->find($another_product->id));

        $this->assertFalse(Storage::exists($another_product->productTypes->first()->productImages->first()->file_path));
        $this->assertFalse(Storage::exists($another_product->productTypes->first()->preview_image));
    }

    /**@test */
    public function test_a_product_can_be_published_with_premissions(): void
    {
        $user = User::query()->first();
        $product = $user->products()->first();
        $another_product = Product::query()->where('saler_id', '!=', $user->id)->first();

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

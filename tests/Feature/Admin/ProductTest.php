<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\OptionValue;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\PropertyValue;
use App\Models\Tag;
use App\Models\User;
use Arr;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class ProductTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_a_product_can_be_viewed_any_with_premissions(): void
    {
        $user = User::query()->first();
        $route = route('admin.products.index');

        $this->get($route)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->get($route)->assertNotFound();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertViewIs('admin.product.index');
        }
    }

    /**@test */
    public function test_a_product_can_be_viewed_with_premissions(): void
    {
        $user = User::query()->has('products')->first();
        $product = $user->products()->first();
        $another_product = Product::query()->firstWhere('saler_id', '!=', $user->id);
        $route = route('admin.products.show', $product->id);
        $another_route = route('admin.products.show', $another_product->id);

        $this->get($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->get($another_route)->assertNotFound();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertViewIs('admin.product.show');
        }
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($another_route)->assertViewIs('admin.product.show');
    }

    /**@test */
    public function test_a_product_can_be_create_index_with_premissions(): void
    {
        $user = User::query()->first();
        $route = route('admin.products.create');

        $this->get($route)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->get($route)->assertNotFound();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route)->assertViewIs('admin.product.create.index');
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
        $route = route('admin.products.create.relations');

        $this->get($route . '?' . $data)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->get($route . '?' . $data)->assertNotFound();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route . '?' . $data)->assertViewIs('admin.product.create.relations');
        }
    }

    /**@test */
    public function test_a_product_can_be_create_types_with_premissions(): void
    {
        $user = User::query()->first();
        $data['tags'] = Tag::query()->take(3)->pluck('id')->all();
        $data['propertyValues'] = PropertyValue::query()->groupBy('property_id')->take(2)->pluck('value',
            'property_id')->all();
        $data['optionValues'] = OptionValue::query()->take(2)->select('option_id', 'id')->toBase()->get()
            ->groupBy('option_id')->transform(fn(Collection $optionValue) => $optionValue->pluck('id'))->toArray();
        $data = http_build_query($data);
        $route = route('admin.products.create.types');

        $this->get($route . '?' . $data)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->get($route . '?' . $data)->assertNotFound();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->from(route('admin.products.create.relations'))
                ->get($route . '?' . $data)
                ->assertViewIs('admin.product.create.types');
        }
    }

    /**@test */
    public function test_a_product_can_be_stored_with_premissions(): void
    {
        $user = User::query()->first();
        $route = route('admin.products.store');

        $session = [
            'create' => [
                'product' => [
                    'title' => '1',
                    'description' => '1',
                    'category_id' => Category::query()->first()->id,
                    'saler_id' => $user->id,
                ],
                'relations' => [
                    'tags' => Tag::query()->take(3)->pluck('id')->all(),
                    'propertyValues' => PropertyValue::query()->groupBy('property_id')->take(2)->pluck('value',
                        'property_id')->all(),
                    'optionValues' => OptionValue::query()->take(2)->pluck('id')->all(),
                ],
            ]
        ];

        $data['types'] = [
            [
                'price' => 1,
                'count' => 1,
                'is_published' => false,
                'preview_image' => File::create('preview_image.jpeg'),
                'relations' => [
                    'productImages' => [File::create('productImage.jpeg')],
                    'optionValues' => [OptionValue::query()->first()->id],
                ]
            ], [
                'price' => 2,
                'count' => 2,
                'is_published' => false,
                'preview_image' => File::create('preview_image.jpeg'),
                'relations' => [
                    'productImages' => [File::create('productImage.jpeg')],
                    'optionValues' => [OptionValue::query()->inRandomOrder()->first()->id],
                ]
            ],
        ];

        session($session);
        $this->post($route, $data)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        session($session);
        $this->actingAs($user)->post($route, $data)->assertNotFound();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $session['create']['product']['title'] = Str::random(5);
            session($session);
            $this->actingAs($user)->post($route, $data);
            $product = Product::query()
                ->withCount(['optionValues', 'propertyValues', 'productTypes', 'tags'])
                ->firstWhere('title', $session['create']['product']['title']);

            $this->assertCount($product->product_types_count, $data['types']);
            $this->assertCount($product->tags_count, $session['create']['relations']['tags']);
            $this->assertCount($product->property_values_count, $session['create']['relations']['propertyValues']);
            $this->assertCount($product->option_values_count, $session['create']['relations']['optionValues']);

            $product->productTypes()
                ->select('id', 'preview_image')
                ->withExists(['optionValues', 'ProductImages'])
                ->get()
                ->each(function (ProductType $productType) {
                    $this->assertModelExists($productType);
                    /** @phpstan-ignore-next-line */
                    /** @noinspection PhpUndefinedFieldInspection */
                    $this->assertTrue($productType->product_images_exists && $productType->option_values_exists);
                    Storage::assertExists($productType->preview_image);
                });
            $this->actingAs($user)->delete(route('admin.products.destroy', $product->id));
            $this->assertModelMissing($product);
        }
    }

    /**@test */
    public function test_a_product_can_be_edit_index_with_premissions(): void
    {
        $user = User::query()->has('products')->first();
        $product = $user->products()->first();
        $another_product = Product::query()->firstWhere('saler_id', '!=', $user->id);
        $route = route('admin.products.edit', $product->id);
        $another_route = route('admin.products.edit', $another_product->id);
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
                ->assertViewIs('admin.product.edit.index');
        }

        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get($another_route)
            ->assertViewIs('admin.product.edit.index');
    }

    /**@test */
    public function test_a_product_can_be_edit_relations_with_premissions(): void
    {
        $user = User::query()->has('products')->first();
        $product = $user->products()->first();
        $data = [
            'title' => '1',
            'description' => '1',
            'category_id' => Category::query()->first()->id,
            'saler_id' => $user->id,
        ];
        $data = http_build_query($data);
        $route = route('admin.products.edit.relations', $product->id);

        $this->get($route . '?' . $data)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->get($route . '?' . $data)->assertNotFound();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->get($route . '?' . $data)
                ->assertViewIs('admin.product.edit.relations');
        }
    }

    /**@test */
    public function test_a_product_can_be_updated_with_premissions(): void
    {
        $user = User::query()->has('products')->first();
        $product = $user->products()->first();
        $another_product = Product::query()->firstWhere('saler_id', '!=', $user->id);
        $session = [
            'edit' => [
                'title' => '1',
                'description' => '1',
                'category_id' => Category::query()->first()->id,
                'saler_id' => $user->id,
            ]
        ];
        $data['tags'] = Tag::query()->take(3)->pluck('id')->all();
        $data['propertyValues'] = PropertyValue::query()->groupBy('property_id')->take(2)->pluck('value',
            'property_id')->all();
        $data['optionValues'] = OptionValue::query()->take(2)->select('option_id', 'id')->toBase()->get()
            ->groupBy('option_id')->transform(fn(Collection $optionValue) => $optionValue->pluck('id'))->toArray();
        $route = route('admin.products.update', $another_product->id);

        session($session);
        $this->patch($route, $data)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        session($session);
        $this->actingAs($user)->patch($route, $data)->assertNotFound();

        session(['user.role' => User::ROLE_SALER]);
        $user->update(['role' => User::ROLE_SALER]);
        session($session);
        $this->actingAs($user)->patch($route, $data)->assertForbidden();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $data['tags'] = Tag::query()->inRandomOrder()->take(3)->pluck('id')->all();
            $data['propertyValues'] = PropertyValue::query()->inRandomOrder()->groupBy('property_id')->take(2)->pluck('value',
                'property_id')->all();
            $data['optionValues'] = OptionValue::query()->inRandomOrder()->take(2)->select('option_id', 'id')->toBase()->get()
                ->groupBy('option_id')->transform(fn(Collection $optionValue) => $optionValue->pluck('id'))->toArray();
            $session['edit']['title'] = Str::random(5);
            session($session);

            $this->actingAs($user)->patch(route('admin.products.update', $product->id), $data)
                ->assertRedirectToRoute('admin.products.show', $product->id);
            $product->refresh()->load('propertyValues', 'optionValues');
            $this->assertEquals($session['edit']['title'], $product->title);
            $this->assertEmpty($product->propertyValues->pluck('value', 'property_id')->diffAssoc($data['propertyValues']));
            $this->assertEmpty($product->optionValues->pluck('id')->diff(Arr::flatten($data['optionValues'], 1)));
        }
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $data['tags'] = Tag::query()->inRandomOrder()->take(3)->pluck('id')->all();
        $data['propertyValues'] = PropertyValue::query()->inRandomOrder()->groupBy('property_id')->take(2)->pluck('value',
            'property_id')->all();
        $data['optionValues'] = OptionValue::query()->inRandomOrder()->take(2)->select('option_id', 'id')->toBase()->get()
            ->groupBy('option_id')->transform(fn(Collection $optionValue) => $optionValue->pluck('id'))->all();
        $session['edit']['title'] = Str::random(5);
        session($session);
        $this->actingAs($user)->patch(route('admin.products.update', $another_product->id), $data)
            ->assertRedirectToRoute('admin.products.show', $another_product->id);

        $another_product->refresh()->load('propertyValues', 'optionValues');
        $this->assertEquals($session['edit']['title'], $another_product->title);
        $this->assertEmpty($another_product->propertyValues->pluck('value', 'property_id')->diffAssoc($data['propertyValues']));
        $this->assertEmpty($another_product->optionValues->pluck('id')->diff(Arr::flatten($data['optionValues'], 1)));

    }

    /**@test */
    public function test_a_product_can_be_deleted_with_premissions(): void
    {
        $user = User::query()->has('products')->first();
        $another_product = Product::query()
            ->with('productTypes.productImages')
            ->firstWhere('saler_id', '!=', $user->id);
        $another_route = route('admin.products.destroy', $another_product->id);

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
            $product = $user->products()->first();
            $this->actingAs($user)->delete(route('admin.products.destroy', $product->id))->assertViewIs('admin.product.index');
            $product->load(['optionValues', 'propertyValues', 'tags', 'productTypes']);
            $this->assertEmpty($product->optionValues);
            $this->assertEmpty($product->propertyValues);
            $this->assertEmpty($product->tags);
            $this->assertEmpty($product->productTypes);
            $this->assertModelMissing($product);
        }
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->delete($another_route)->assertViewIs('admin.product.index');
        $another_product->load(['optionValues', 'propertyValues', 'tags', 'productTypes']);
        $this->assertEmpty($another_product->optionValues);
        $this->assertEmpty($another_product->propertyValues);
        $this->assertEmpty($another_product->tags);
        $this->assertEmpty($another_product->productTypes);
        $this->assertModelMissing($another_product);
    }

    /**@test */
    public function test_a_product_can_be_published_with_premissions(): void
    {
        $user = User::query()->has('products')->first();
        $product = $user->products()->first();
        $another_product = Product::query()->firstWhere('saler_id', '!=', $user->id);
        $route = route('admin.products.publish', $product->id);
        $another_route = route('admin.products.publish', $another_product->id);
        $this->patch($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_CLIENT]);
        $user->update(['role' => User::ROLE_CLIENT]);
        $this->actingAs($user)->patch($another_route)->assertNotFound();

        session(['user.role' => User::ROLE_SALER]);
        $user->update(['role' => User::ROLE_SALER]);
        $this->actingAs($user)->patch($another_route)->assertForbidden();

        $this->withoutExceptionHandling();

        for ($i = 1; $i <= 2; $i++) {
            $product->productTypes()->update(['is_published' => 1]);
            $user->update(['role' => $i]);
            session(['user.role' => $i]);
            $this->actingAs($user)->patch($route)->assertRedirect();
            $this->assertFalse($product->productTypes()->where('is_published', 1)->exists());
            $this->actingAs($user)->patch($route, ['publish' => 'on'])->assertRedirect();
            $this->assertFalse($product->productTypes()->where('is_published', 0)->exists());
        }
        $product->productTypes()->update(['is_published' => 1]);
        session(['user.role' => User::ROLE_ADMIN]);
        $user->update(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->patch($another_route)->assertRedirect();
        $this->assertFalse($another_product->productTypes()->where('is_published', 1)->exists());
        $this->actingAs($user)->patch($another_route, ['publish' => 'on'])->assertRedirect();
        $this->assertFalse($another_product->productTypes()->where('is_published', 0)->exists());
    }
}

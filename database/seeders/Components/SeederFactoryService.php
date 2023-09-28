<?php

namespace Database\Seeders\Components;

use App\Models\Category;
use App\Models\CommentImage;
use App\Models\Option;
use App\Models\OptionValue;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;
use App\Models\Property;
use App\Models\PropertyValue;
use App\Models\RatingAndComment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SeederFactoryService
{
    public function factory(): void
    {
        $tags = Tag::factory(10)->create();
        for ($k = 1; $k <= 10; $k++) {
            Property::factory()
                ->has(PropertyValue::factory(rand(2, 4)))
                ->create();
        }
        foreach (SeederInitialData::getOptions() as $option) {
            Option::query()->create($option);
            OptionValue::factory(rand(3, 4))->create();
        }

        foreach (SeederInitialData::getCategories() as $category) {
            $properties = Property::query()
                ->with(['propertyValues' => fn($q) => $q->select('id', 'property_id')->inRandomOrder()])
                ->inRandomOrder()
                ->take(5)
                ->get('id');

            $options = Option::query()
                ->with(['optionValues' => fn($q) => $q->select('id', 'option_id')->inRandomOrder()])
                ->inRandomOrder()
                ->take(4)
                ->get('id');

            Category::query()->create($category)->properties()->attach($properties);
            User::factory()->create();
            for ($j = 1; $j <= 5; $j++) {
                $propertyValues = $properties->pluck('propertyValues')->shuffle()->pluck(0);
                $optionValues = $options->random(2)->pluck('optionValues')->random(2)
                    ->map(fn(Collection $optionValue) => $optionValue->random(2))->flatten();

                Product::factory()
                    ->has(ProductType::factory(4)
                        ->has(ProductImage::factory(3)))
                    ->has(RatingAndComment::factory()
                        ->has(CommentImage::factory()))
                    ->hasAttached($tags->random(rand(3, 5)))
                    ->hasAttached($optionValues)
                    ->hasAttached($propertyValues)
                    ->create();
            }
        }
        for ($m = 1; $m <= 5; $m++) {
            Order::factory()->create();
            for ($n = 0; $n < count(Cache::get('saler_ids_for_factory_order_performers')); $n++) {
                OrderPerformer::factory()->create();
            }
        }
    }
}

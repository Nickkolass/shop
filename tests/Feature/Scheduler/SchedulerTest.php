<?php

namespace Scheduler;

use App\Jobs\Scheduler\DBCleanUpdateJob;
use App\Models\Product;
use App\Models\ProductType;
use Tests\Feature\Trait\PrepareForTestWithSeedTrait;
use Tests\TestCase;

class SchedulerTest extends TestCase
{

    use PrepareForTestWithSeedTrait;

    /**@test */
    public function test_db_clean_update_job(): void
    {
        $this->withoutExceptionHandling();
        $product = Product::query()->first();
        $productType = ProductType::query()->first();

        $count_likes = $productType->count_likes;
        $productType->increment('count_likes');

        $rating = $product->rating;
        $count_rating = $product->count_rating;
        $count_comments = $product->count_comments;
        $product->increment('rating');
        $product->increment('count_rating');
        $product->increment('count_comments');

        dispatch(new DBCleanUpdateJob());
        $product->refresh();
        $productType->refresh();
        $this->assertTrue($productType->count_likes == $count_likes);
        $this->assertTrue($product->rating == $rating);
        $this->assertTrue($product->count_rating == $count_rating);
        $this->assertTrue($product->count_comments == $count_comments);
    }
}

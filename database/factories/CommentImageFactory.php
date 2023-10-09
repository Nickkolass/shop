<?php

namespace Database\Factories;

use App\Models\ProductImage;
use App\Models\RatingAndComment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class CommentImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<mixed>
     */
    public function definition(): array
    {
        $image = ProductImage::query()
            ->latest('productType_id')
            ->first();

        $comment_image_path = str_replace('product_images', 'comment_images', $image->file_path);
        Storage::copy($image->file_path, $comment_image_path);

        return [
            'file_path' => $comment_image_path,
            'size' => $image->size,
            'comment_id' => RatingAndComment::query()->take(1)->latest('id')->pluck('id')['0'],
        ];
    }
}

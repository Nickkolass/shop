<?php

namespace Database\Factories;

use App\Models\ProductType;
use App\Models\RatingAndComment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommentImage>
 */
class CommentImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $image = ProductType::latest('id')->first()->productImages()->inRandomOrder()->first();
        $file_path = str_replace('product_images/', 'comments/', $image->file_path);

        Storage::copy('public/' . $image->file_path, 'public/' . $file_path);

        return [
            'file_path' => $file_path,
            'size' => $image->size,
            'comment_id' => RatingAndComment::latest('id')->pluck('id')['0'],
        ];
    }
}

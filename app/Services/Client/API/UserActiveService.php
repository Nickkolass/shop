<?php

namespace App\Services\Client\API;

use App\Models\CommentImage;
use App\Models\ProductType;
use App\Models\RatingAndComment;
use App\Services\Admin\Product\ImageService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UserActiveService
{

    public function likedToggle(ProductType $productType, int $User_id): void
    {
        $productType->liked()->toggle($User_id);
    }

    /**
     * @param array<empty>|array<int, mixed> $data
     * @return void
     */
    public function commentStore(array $data): void
    {
        DB::beginTransaction();
        try {
            if (empty($data['comment_images'])) RatingAndComment::query()->create($data);
            else {
                $comment_images = [];
                foreach ($data['comment_images'] as $img) $comment_images[] = new UploadedFile(...$img);
                unset($data['comment_images']);
                $comment_id = RatingAndComment::query()->create($data)->id;

                foreach ($comment_images as &$image) {
                    $image = [
                        'comment_id' => $comment_id,
                        'size' => $image->getSize(),
                        'file_path' => $image->storePublicly('comment_images/' . $data['product_id']),
                    ];
                }
                CommentImage::query()->insert($comment_images);
            }
            DB::commit();
        } catch (Exception $e) {
            if (isset($comment_images)) ImageService::deleteImages(array_column($comment_images, 'file_path'));
            report($e);
            abort(back()->withErrors([$e->getMessage()])->withInput());
        }
    }
}

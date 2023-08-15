<?php

namespace App\Services\API\Back;

use App\Models\CommentImage;
use App\Models\ProductType;
use App\Models\RatingAndComment;
use App\Services\Admin\Product\ImageService;
use App\Services\Methods\Maper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BackService
{

    public function getViewed(array $productType_ids): Collection
    {
        $productTypes = ProductType::query()
            ->select('id', 'product_id', 'is_published', 'preview_image', 'price', 'count')
            ->with([
                'productImages:productType_id,file_path',
                'optionValues.option:id,title',
                'product' => function ($q) {
                    $q->select('id', 'title')
                        ->with([
                            'productTypes:id,product_id,is_published,preview_image',
                            'ratingAndComments',
                        ]);
                }])
            ->find($productType_ids);

        Maper::mapAfterGettingProducts($productTypes);
        return $productTypes;
    }

    public function getLiked(): ?Collection
    {
        $productTypes = auth('api')->user()->liked()
            ->select('productTypes.id', 'product_id', 'is_published', 'preview_image', 'price', 'count')
            ->with([
                'productImages:productType_id,file_path',
                'optionValues.option:id,title',
                'product' => function ($q) {
                    $q->select('id', 'title')
                        ->with([
                            'productTypes:id,product_id,is_published,preview_image',
                            'ratingAndComments',
                        ]);
                }])
            ->get();

        Maper::mapAfterGettingProducts($productTypes);
        return $productTypes->count() ? $productTypes : null;
    }

    public function product(ProductType &$productType): void
    {
        $user_id = auth('api')->id() ?? null;
        $productType
            ->loadExists(['liked' => function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            }])
            ->load([
                'productImages:productType_id,file_path',
                'optionValues.option:id,title',
                'product' => function ($product) use ($user_id) {
                    $product
                        ->withExists(['ratingAndComments' => function ($q) use ($user_id) {
                            $q->where('user_id', $user_id);
                        }])
                        ->with([
                            'optionValues.option:id,title',
                            'category:id,title,title_rus',
                            'propertyValues.property:id,title',
                            'productTypes:id,product_id,is_published,preview_image',
                            'ratingAndComments' => function ($q) {
                                $q->with([
                                    'user:id,name',
                                    'commentImages:comment_id,file_path'
                                ]);
                            }
                        ]);
                }]);
        Maper::valuesToKeys($productType->product, 'propertyValues');
        Maper::valuesToKeys($productType, 'optionValues');
        Maper::countingRatingAndComments($productType->product);
    }

    public function cart(?array $cart): ?Collection
    {
        $productTypes = ProductType::query()
            ->select('id', 'product_id', 'price', 'count', 'preview_image', 'is_published')
            ->with([
                'optionValues.option:id,title',
                'product:id,title'
            ])
            ->find(array_keys($cart))
            ->each(function (ProductType $productType) use ($cart) {
                $productType->amount = $cart[$productType->id];
                $productType->totalPrice = $productType->amount * $productType->price;
                Maper::valuesToKeys($productType, 'optionValues');
            });
        return $productTypes;
    }

    public function commentStore(array $data): void
    {
        DB::beginTransaction();
        try {
            if (empty($data['comment_images'])) RatingAndComment::create($data);
            else {
                foreach ($data['comment_images'] as $img) $comment_images[] = new UploadedFile(...$img);
                unset($data['comment_images']);
                $comment_id = RatingAndComment::create($data)->id;

                foreach ($comment_images as &$image) {
                    $image = [
                        'comment_id' => $comment_id,
                        'size' => $image->getSize(),
                        'file_path' => $image->storePublicly('comments/' . $data['product_id'] . '/' . $comment_id, 'public'),
                    ];
                }
                CommentImage::insert($comment_images);
            }
            DB::commit();
        } catch (\Exception $e) {
            if (isset($comment_images)) ImageService::deleteImages(array_column($comment_images, 'file_path'));
            report($e);
            abort(back()->withErrors([$e->getMessage()])->withInput());
        }
    }
}

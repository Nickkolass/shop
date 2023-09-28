<?php

namespace App\Dto\Admin\Product;

use Illuminate\Http\UploadedFile;

class ProductTypeRelationDto
{
    /**
     * @param array<int, int> $optionValues
     * @param array<int, UploadedFile> $productImages
     */
    public function __construct(
        public readonly array $optionValues,
        public readonly array $productImages = [],
    )
    {
    }
}

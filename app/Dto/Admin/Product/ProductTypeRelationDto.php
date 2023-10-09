<?php

namespace App\Dto\Admin\Product;

use Illuminate\Http\UploadedFile;

class ProductTypeRelationDto
{
    /**
     * @param array<int> $optionValues
     * @param array<UploadedFile> $productImages
     */
    public function __construct(
        public readonly array $optionValues,
        public readonly array $productImages = [],
    )
    {
    }
}

<?php

namespace App\Dto\Admin\Product;

use Illuminate\Http\UploadedFile;

class ProductTypeDto
{
    /**
     * @param ProductTypeRelationDto $productTypeRelationDto rewritable
     * @param UploadedFile|string|null $preview_image rewritable
     */
    public function __construct(
        public readonly int             $price,
        public readonly int             $count,
        public ProductTypeRelationDto   $productTypeRelationDto,
        public readonly ?bool           $is_published = null,
        public UploadedFile|string|null $preview_image = null,
    )
    {
    }
}

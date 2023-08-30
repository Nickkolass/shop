<?php

namespace App\Dto\Admin\Product;

use Illuminate\Http\UploadedFile;

class ProductTypeDto
{
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

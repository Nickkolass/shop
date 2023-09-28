<?php

namespace App\Dto\Admin\Product;

class ProductDto
{
    public function __construct(
        public readonly int    $saler_id,
        public readonly int    $category_id,
        public readonly string $title,
        public readonly string $description,
    )
    {
    }
}

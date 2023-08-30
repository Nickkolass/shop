<?php

namespace App\Dto\Admin\Product;

class ProductTypeRelationDto
{
    public function __construct(
        public readonly array $optionValues,
        public readonly array $productImages = [],
    )
    {
    }
}

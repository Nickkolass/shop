<?php

namespace App\Dto\Admin\Product;

class ProductRelationDto
{
    public function __construct(
        public array $propertyValues,
        public readonly array $optionValues,
        public readonly array $tags,
    )
    {
    }
}

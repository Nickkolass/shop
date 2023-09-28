<?php

namespace App\Dto\Admin\Product;

class ProductRelationDto
{
    /**
     * @param array<int, string> $propertyValues rewritable
     * @param array<int, int> $optionValues
     * @param array<int, int> $tags
     */
    public function __construct(
        public array          $propertyValues,
        public readonly array $optionValues,
        public readonly array $tags,
    )
    {
    }
}

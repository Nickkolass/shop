<?php

namespace App\Dto\Admin\Product;

class ProductRelationDto
{
    /**
     * @param array<string> $propertyValues rewritable
     * @param array<int> $optionValues
     * @param array<int> $tags
     */
    public function __construct(
        public array          $propertyValues,
        public readonly array $optionValues,
        public readonly array $tags,
    )
    {
    }
}

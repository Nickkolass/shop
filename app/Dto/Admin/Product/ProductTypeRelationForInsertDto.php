<?php

namespace App\Dto\Admin\Product;

class ProductTypeRelationForInsertDto
{
    /**
     * @param array<string, int|string>|array<empty> $optionValues
     * @param array<int, array<string, int|string>> $productImages
     */
    public function __construct(
        public readonly array $optionValues,
        public readonly array $productImages,
    )
    {
    }
}

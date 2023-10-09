<?php

namespace App\Dto\Admin\Product;

class ProductTypeRelationForInsertDto
{
    /**
     * @param array<array<string, int|string>>|array{} $optionValues
     * @param array<array<string, int|string>> $productImages
     */
    public function __construct(
        public readonly array $optionValues,
        public readonly array $productImages,
    )
    {
    }
}

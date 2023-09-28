<?php

namespace App\Dto\Admin;

class PropertyDto
{
    /**
     * @param array<int, int> $category_ids
     * @param array<int, string> $propertyValues
     */
    public function __construct(
        public readonly string $title,
        public readonly array  $category_ids,
        public readonly array  $propertyValues,
    )
    {
    }
}

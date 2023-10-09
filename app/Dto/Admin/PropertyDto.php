<?php

namespace App\Dto\Admin;

class PropertyDto
{
    /**
     * @param string $title
     * @param array<int> $category_ids
     * @param array<string> $propertyValues
     */
    public function __construct(
        public readonly string $title,
        public readonly array  $category_ids,
        public readonly array  $propertyValues,
    )
    {
    }
}

<?php

namespace App\Dto\Admin;

class PropertyDto
{
    public function __construct(
        public readonly string $title,
        public readonly array $category_ids,
        public readonly array $propertyValues,
    )
    {
    }
}

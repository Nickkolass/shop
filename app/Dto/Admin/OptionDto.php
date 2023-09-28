<?php

namespace App\Dto\Admin;

class OptionDto
{
    /** @param array<int, int> $optionValues */
    public function __construct(
        public readonly string $title,
        public readonly array  $optionValues,
    )
    {
    }
}

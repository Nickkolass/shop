<?php

namespace App\Dto\Admin;

class OptionDto
{
    /**
     * @param string $title
     * @param array<int> $optionValues
     */
    public function __construct(
        public readonly string $title,
        public readonly array  $optionValues,
    )
    {
    }
}

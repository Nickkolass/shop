<?php

namespace App\Dto\Admin;

class OptionDto
{
    public function __construct(
        public readonly string $title,
        public readonly array $optionValues,
    )
    {
    }
}

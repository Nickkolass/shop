<?php

namespace App\Dto\Admin;

class UserDto
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $email,
        public readonly string  $surname,
        public readonly int     $age,
        public readonly ?string  $patronymic = null,
        public readonly ?int    $INN = null,
        public readonly ?string $registredOffice = null,
        public readonly ?int    $card = null,
        public readonly ?bool   $gender = null,
        public readonly ?int    $role = null,
    )
    {
    }
}

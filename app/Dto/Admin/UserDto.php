<?php

namespace App\Dto\Admin;

class UserDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $surname,
        public readonly string $patronymic,
        public readonly int    $age,
        public readonly int    $INN,
        public readonly string $registredOffice,
        public readonly ?bool  $gender = null,
        public readonly ?int   $role = null,
    )
    {
    }
}

<?php

namespace App\Dto;

readonly class UserNameDto
{
    public function __construct(
        public string $firstName,
        public string $lastName,
    ) {
    }
}

<?php

namespace App\DTO\Output;

class RegisterUserOutput
{
    public function __construct(
        public int $userId
    ) {}
}

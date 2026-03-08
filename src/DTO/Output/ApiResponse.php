<?php

declare(strict_types=1);

namespace App\DTO\Output;

class ApiResponse
{
    public function __construct(
        public readonly mixed $data,
        public readonly string $message,
        public readonly ?array $metadata = null,
    ) {}
}

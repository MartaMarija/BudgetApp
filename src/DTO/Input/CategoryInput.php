<?php

declare(strict_types=1);

namespace App\DTO\Input;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(required: ['name'])]
class CategoryInput
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[OA\Property(example: 'Food')]
    public string $name;
}

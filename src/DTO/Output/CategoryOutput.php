<?php

declare(strict_types=1);

namespace App\DTO\Output;

use App\Entity\Category;
use OpenApi\Attributes as OA;

#[OA\Schema]
class CategoryOutput
{
    public function __construct(
        #[OA\Property(example: 1)]
        public readonly int $id,

        #[OA\Property(example: 'Food')]
        public readonly string $name,

        #[OA\Property(example: true)]
        public readonly bool $canDelete,
    ) {}

    public static function serialize(Category $category): CategoryOutput
    {
        return new CategoryOutput(
            $category->getId(),
            $category->getName(),
            !empty($category->getCreatedBy()),
        );
    }

    /**
     * @param Category[] $categories
     */
    public static function list(array $categories): array
    {
        return array_map(function(Category $category) {
            return self::serialize($category);
        }, $categories);
    }
}

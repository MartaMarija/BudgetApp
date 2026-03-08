<?php

declare(strict_types=1);

namespace App\DTO\Input\Transaction;

use App\Enum\TransactionType;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(required: ['amount', 'date', 'type'])]
class TransactionInput
{
    private const array TRANSACTION_TYPES = [
        TransactionType::INCOME->value,
        TransactionType::EXPENSE->value,
    ];

    #[Assert\NotNull]
    #[OA\Property(example: 250.00)]
    public float $amount;

    #[Assert\NotBlank]
    #[Assert\Date]
    #[OA\Property(format: 'date', example: '2024-03-15')]
    public string $date;

    #[Assert\Length(max: 255)]
    #[OA\Property(example: 'Grocery shopping', nullable: true)]
    public ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::TRANSACTION_TYPES)]
    #[OA\Property(enum: self::TRANSACTION_TYPES, example: 'expense')]
    public string $type;

    #[Assert\NotBlank]
    #[OA\Property(example: 3, nullable: true)]
    public int $categoryId;
}

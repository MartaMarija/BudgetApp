<?php

declare(strict_types=1);

namespace App\DTO\Output\Transaction;

use App\Entity\Transaction;
use OpenApi\Attributes as OA;

#[OA\Schema]
class TransactionOutput
{
    public function __construct(
        #[OA\Property(example: 1)]
        public readonly int $id,

        #[OA\Property(example: '250.00')]
        public readonly string $amount,

        #[OA\Property(example: '2024-03-15')]
        public readonly string $date,

        #[OA\Property(example: 'Grocery shopping', nullable: true)]
        public readonly ?string $description,

        #[OA\Property(enum: ['income', 'expense'], example: 'expense')]
        public readonly string $type,

        #[OA\Property(example: 3, nullable: true)]
        public readonly ?int $categoryId,

        #[OA\Property(example: 'Food', nullable: true)]
        public readonly ?string $categoryName,
    ) {}

    public static function serialize(Transaction $transaction): TransactionOutput
    {
        return new TransactionOutput(
            id: $transaction->getId(),
            amount: number_format($transaction->getAmount(), 2),
            date: $transaction->getDate()->format('Y-m-d'),
            description: $transaction->getDescription(),
            type: $transaction->getType()->value,
            categoryId: $transaction->getCategory()?->getId(),
            categoryName: $transaction->getCategory()?->getName(),
        );
    }

    /**
     * @param Transaction[] $transactions
     */
    public static function list(array $transactions): array
    {
        return array_map(function (Transaction $transaction) {
            return self::serialize($transaction);
        }, $transactions);
    }
}

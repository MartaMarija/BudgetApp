<?php

declare(strict_types=1);

namespace App\DTO\Input\Transaction;

use App\Enum\TransactionType;
use DateTimeImmutable;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[OA\Schema(required: [])]
class TransactionFilterInput
{
    private const array DATE_RANGE_KEYS = ['last_month', 'quarter', 'year'];
    private const array DATE_RANGES = [
        'last_month' => [
            'from' => 'first day of last month 00:00:00',
            'to' => 'last day of last month 23:59:59',
        ],
        'quarter' => [
            'from' => 'first day of -3 months 00:00:00',
            'to' => 'now',
        ],
        'year' => [
            'from' => 'first day of January this year 00:00:00',
            'to' => 'last day of December this year 23:59:59',
        ],
    ];

    private const array TRANSACTION_TYPES = [
        TransactionType::INCOME->value,
        TransactionType::EXPENSE->value,
    ];

    #[Assert\Choice(choices: self::DATE_RANGE_KEYS)]
    #[OA\Property(
        description: 'Date range',
        enum: self::DATE_RANGE_KEYS,
        nullable: true,
    )]
    public ?string $dateRange = null;

    #[Assert\Date]
    #[OA\Property(
        description: 'Start date (Y-m-d)',
        format: 'date',
        nullable: true,
    )]
    public ?string $from = null;

    #[Assert\Date]
    #[OA\Property(
        description: 'End date (Y-m-d)',
        format: 'date',
        nullable: true,
    )]
    public ?string $to = null;

    #[Assert\Choice(choices: self::TRANSACTION_TYPES)]
    #[OA\Property(enum: self::TRANSACTION_TYPES, nullable: true)]
    public ?string $type = null;

    #[OA\Property(example: 3, nullable: true)]
    public ?int $categoryId = null;

    #[Assert\Positive]
    #[OA\Property(default: 1, example: 1)]
    public int $page = 1;

    #[Assert\Range(min: 1, max: 100)]
    #[OA\Property(default: 20, example: 20)]
    public int $limit = 20;

    private const array SORT_FIELDS = ['amount', 'date'];

    #[Assert\Choice(choices: self::SORT_FIELDS)]
    #[OA\Property(enum: self::SORT_FIELDS, nullable: true)]
    public string $sortField = 'date';

    #[Assert\Choice(choices: ['asc', 'desc'])]
    #[OA\Property(enum: ['asc', 'desc'], nullable: true)]
    public string $sortDirection = 'asc';

    #[Assert\Callback]
    public function validateDateInput(ExecutionContextInterface $context): void
    {
        if ($this->dateRange !== null && ($this->from !== null || $this->to !== null)) {
            $context->buildViolation('Provide either `range` or `from`/`to`, not both.')
                ->atPath('dateRange')
                ->addViolation();

            return;
        }

        if (($this->from === null) !== ($this->to === null)) {
            $context->buildViolation('Both `from` and `to` must be provided together.')
                ->atPath('from')
                ->addViolation();
        }
    }

    public function getFrom(): ?DateTimeImmutable
    {
        if ($this->dateRange !== null) {
            return new DateTimeImmutable(self::DATE_RANGES[$this->dateRange]['from']);
        }

        if ($this->from !== null) {
            return new DateTimeImmutable($this->from . ' 00:00:00');
        }

        return null;
    }

    public function getTo(): ?DateTimeImmutable
    {
        if ($this->dateRange !== null) {
            return new DateTimeImmutable(self::DATE_RANGES[$this->dateRange]['to']);
        }

        if ($this->from !== null) {
            return new DateTimeImmutable($this->to . ' 23:59:59');
        }

        return null;
    }

    public function getTransactionType(): ?TransactionType
    {
        if (empty($this->type)) {
            return null;
        }

        return TransactionType::from($this->type);
    }
}

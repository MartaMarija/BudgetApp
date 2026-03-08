<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionType;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function save(Transaction $transaction): Transaction
    {
        $this->getEntityManager()->persist($transaction);
        $this->getEntityManager()->flush();

        return $transaction;
    }

    public function delete(Transaction $transaction): void
    {
        $this->getEntityManager()->remove($transaction);
        $this->getEntityManager()->flush();
    }

    public function getFilterQuery(
        User               $user,
        ?DateTimeInterface $from,
        ?DateTimeInterface $to,
        ?TransactionType   $type,
        ?int               $categoryId,
        ?string            $sortField = null,
        ?string            $sortDirection = null,
    ): QueryBuilder {
        $qb = $this
            ->createQueryBuilder('transactions')
            ->select('transactions')
            ->where('transactions.createdBy = :userId')
            ->setParameter('userId', $user->getId())
        ;

        if ($from !== null && $to !== null) {
            $qb
                ->andWhere('transactions.date BETWEEN :from AND :to')
                ->setParameter('from', $from)
                ->setParameter('to', $to)
            ;
        }

        if ($type !== null) {
            $qb
                ->andWhere('transactions.type = :type')
                ->setParameter('type', $type);
        }

        if ($categoryId !== null) {
            $qb
                ->leftJoin('transactions.category', 'categories')
                ->andWhere('categories.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        if ($sortDirection !== null) {
            $field = "transactions.$sortField";
            $qb->orderBy($field, $sortDirection);
        }

        return $qb;
    }

    public function getAmountStats(
        User               $user,
        ?DateTimeInterface $from,
        ?DateTimeInterface $to,
        ?TransactionType   $type,
        ?int               $categoryId,
    ): array {
        $qb = $this->getFilterQuery($user, $from, $to, $type, $categoryId);

        $qb
            ->select('transactions.type, MIN(transactions.amount) as min, MAX(transactions.amount) as max')
            ->groupBy('transactions.type')
        ;

        $rows = $qb->getQuery()->getResult();

        $stats = [
            'income'  => ['min' => null, 'max' => null],
            'expense' => ['min' => null, 'max' => null],
        ];

        foreach ($rows as $row) {
            $type = $row['type']->value;

            $stats[$type]['min'] = number_format($row['min'], 2);;
            $stats[$type]['max'] = number_format($row['max'], 2);
        }

        return $stats;
    }

    public function getTotalAmount(
        User               $user,
        ?DateTimeInterface $from,
        ?DateTimeInterface $to,
        ?TransactionType   $type,
        ?int               $categoryId,
    ): array {
        $qb = $this->getFilterQuery($user, $from, $to, $type, $categoryId);

        $qb
            ->select('transactions.type, SUM(transactions.amount) as total')
            ->groupBy('transactions.type')
        ;

        $rows = $qb->getQuery()->getResult();

        $totals = [
            'totalIncome' => '0.00',
            'totalExpense' => '0.00'
        ];

        foreach ($rows as $row) {
            if ($row['type'] === TransactionType::INCOME) {
                $totals['totalIncome'] = number_format($row['total'], 2);
            } else {
                $totals['totalExpense'] = number_format($row['total'], 2);
            }
        }

        return $totals;
    }
}

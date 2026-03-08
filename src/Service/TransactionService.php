<?php

namespace App\Service;

use App\DTO\Input\Transaction\TransactionInput;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionType;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use DateTime;

class TransactionService
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {}

    public function create(TransactionInput $input, User $user): Transaction
    {
        $transaction = new Transaction();
        $transaction->setCreatedBy($user);

        return $this->update($transaction, $input);
    }

    public function update(Transaction $transaction, TransactionInput $input): Transaction
    {
        $transaction->setAmount($input->amount);
        $transaction->setDate(new DateTime($input->date));
        $transaction->setDescription($input->description);
        $transaction->setType(TransactionType::from($input->type));

        $category = $this->categoryRepository->find($input->categoryId);
        $transaction->setCategory($category);

        return $this->transactionRepository->save($transaction);
    }
}

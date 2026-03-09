<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference(UserFixtures::USER_REFERENCE, User::class);

        /** @var Category $food */
        $food = $this->getReference(CategoryFixtures::CATEGORY_FOOD, Category::class);
        /** @var Category $transport */
        $transport = $this->getReference(CategoryFixtures::CATEGORY_TRANSPORT, Category::class);
        /** @var Category $salary */
        $salary = $this->getReference(CategoryFixtures::CATEGORY_SALARY, Category::class);
        /** @var Category $entertainment */
        $entertainment = $this->getReference(CategoryFixtures::CATEGORY_ENTERTAINMENT, Category::class);
        /** @var Category $clothes */
        $clothes = $this->getReference(CategoryFixtures::CATEGORY_CLOTHES, Category::class);

        $transactions = [
            ['amount' => 3500.00, 'type' => TransactionType::INCOME,  'category' => $salary,        'date' => '2026-03-01', 'description' => 'Monthly salary'],
            ['amount' => 85.50,   'type' => TransactionType::EXPENSE, 'category' => $food,          'date' => '2026-03-02', 'description' => 'Grocery shopping'],
            ['amount' => 42.00,   'type' => TransactionType::EXPENSE, 'category' => $transport,     'date' => '2026-03-03', 'description' => 'Monthly bus pass'],
            ['amount' => 30.00,   'type' => TransactionType::EXPENSE, 'category' => $entertainment, 'date' => '2026-03-04', 'description' => 'Cinema tickets'],
            ['amount' => 120.00,  'type' => TransactionType::EXPENSE, 'category' => $food,          'date' => '2026-03-05', 'description' => 'Restaurant dinner'],
            ['amount' => 500.00,  'type' => TransactionType::INCOME,  'category' => $salary,        'date' => '2026-03-06', 'description' => 'Freelance project'],
            ['amount' => 15.99,   'type' => TransactionType::EXPENSE, 'category' => $entertainment, 'date' => '2026-03-07', 'description' => 'Streaming subscription'],
            ['amount' => 60.00,   'type' => TransactionType::EXPENSE, 'category' => $transport,     'date' => '2026-03-08', 'description' => 'Fuel'],
            ['amount' => 50.00,   'type' => TransactionType::EXPENSE, 'category' => $clothes,       'date' => '2026-03-08', 'description' => 'Shirt'],
        ];

        foreach ($transactions as $data) {
            $transaction = new Transaction();
            $transaction->setAmount($data['amount']);
            $transaction->setType($data['type']);
            $transaction->setCategory($data['category']);
            $transaction->setCreatedBy($user);
            $transaction->setDate(new \DateTime($data['date']));
            $transaction->setDescription($data['description']);

            $manager->persist($transaction);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}
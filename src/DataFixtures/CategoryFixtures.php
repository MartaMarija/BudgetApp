<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public const string CATEGORY_FOOD = 'category-food';
    public const string CATEGORY_TRANSPORT = 'category-transport';
    public const string CATEGORY_SALARY = 'category-salary';
    public const string CATEGORY_ENTERTAINMENT = 'category-entertainment';
    public const string CATEGORY_CLOTHES = 'category-clothes';

    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference(UserFixtures::USER_REFERENCE, User::class);

        $categories = [
            self::CATEGORY_FOOD => 'Food',
            self::CATEGORY_TRANSPORT => 'Transport',
            self::CATEGORY_SALARY => 'Salary',
            self::CATEGORY_ENTERTAINMENT => 'Entertainment',
        ];

        foreach ($categories as $reference => $name) {
            $category = new Category();
            $category->setName($name);
            $category->setCreatedBy($user);

            $manager->persist($category);
            $this->addReference($reference, $category);
        }

        /** One category without the user. */
        $category = new Category();
        $category->setName('Clothes');

        $manager->persist($category);
        $this->addReference(self::CATEGORY_CLOTHES, $category);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
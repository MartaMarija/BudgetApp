<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const string USER_REFERENCE = 'user-john';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('john@example.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));

        $manager->persist($user);
        $manager->flush();

        $this->addReference(self::USER_REFERENCE, $user);
    }
}
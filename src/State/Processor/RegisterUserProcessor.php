<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\Output\RegisterUserOutput;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterUserProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = new User();

        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setEmail($data->email);

        $password = $this->passwordHasher->hashPassword($user, $data->password);
        $user->setPassword($password);

        $user = $this->userRepository->save($user);

        return new RegisterUserOutput($user->getId());
    }
}

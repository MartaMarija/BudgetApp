<?php

namespace App\Service;

use App\DTO\Input\RegisterUserInput;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function register(RegisterUserInput $input): User
    {
        if ($this->userRepository->findOneByEmail($input->email)) {
            throw new BadRequestHttpException('Email already in use');
        }

        $user = new User();
        $user->setEmail($input->email);
        $user->setFirstName($input->firstName);
        $user->setLastName($input->lastName);

        $password = $this->passwordHasher->hashPassword($user, $input->password);
        $user->setPassword($password);

        return $this->userRepository->save($user);
    }
}
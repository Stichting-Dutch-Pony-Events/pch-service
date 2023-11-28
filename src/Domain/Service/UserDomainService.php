<?php

namespace App\Domain\Service;

use App\Application\Request\UserRequest;
use App\Domain\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDomainService
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function createUser(UserRequest $userRequest): User
    {
        $user = new User(
            name: $userRequest->name,
            username: $userRequest->username,
            password: null,
            roles: $userRequest->roles
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user, $userRequest->password);
        $user->setPassword($hashedPassword);

        return $user;
    }

    public function updateUser(User $user, UserRequest $userRequest): User
    {
        $user->setName($userRequest->name);
        $user->setUsername($userRequest->username);
        if($userRequest->roles !== null) {
            $user->setRoles($userRequest->roles);
        }

        if ($userRequest->password !== null) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userRequest->password);
            $user->setPassword($hashedPassword);
        }

        return $user;
    }
}

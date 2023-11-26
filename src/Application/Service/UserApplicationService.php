<?php

namespace App\Application\Service;

use App\Application\Request\UserRequest;
use App\Domain\Entity\User;
use App\Domain\Service\UserDomainService;
use Doctrine\ORM\EntityManagerInterface;

class UserApplicationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserDomainService $userDomainService
    )
    {
    }

    public function createUser(UserRequest $userRequest): User {
        $user = $this->userDomainService->createUser($userRequest);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
<?php

namespace App\Domain\Entity\Contract;

use App\Security\Enum\RoleEnum;
use Symfony\Component\Security\Core\User\UserInterface;

interface EnumUserInterface extends UserInterface
{
    /**
     * @return RoleEnum[]
     */
    public function getUserRoles(): array;
}
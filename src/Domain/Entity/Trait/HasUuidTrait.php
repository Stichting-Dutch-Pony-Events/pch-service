<?php

namespace App\Domain\Entity\Trait;

use App\Util\Exceptions\Exception\Entity\EntityNotPersistedException;
use Symfony\Component\Uid\Uuid;

trait HasUuidTrait
{
    private ?Uuid $id = null;
    
    /**
     * @throws EntityNotPersistedException
     */
    public function getId(): string
    {
        if ($this->id === null) {
            throw new EntityNotPersistedException("Cannot Access Entity ID before it is persisted.");
        }

        return $this->id->toRfc4122();
    }

    /**
     * @throws EntityNotPersistedException
     */
    public function getUuid(): Uuid
    {
        if ($this->id === null) {
            throw new EntityNotPersistedException("Cannot Access Entity ID before it is persisted.");
        }

        return $this->id;
    }
}
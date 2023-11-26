<?php

namespace App\Util\Validator\Subscriber;

use App\Util\Validator\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ValidatorRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ?EntityManagerInterface $entityManager,
    ) {}

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

    public function onKernelRequest(): void
    {
        Validator::setEntityManager($this->entityManager);
    }
}

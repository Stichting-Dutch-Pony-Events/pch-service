<?php

namespace App\Util\Validator\Listener;

use App\Util\Validator\AbstractRequest;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;

class ControllerEventListener
{
    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        foreach ($event->getArguments() as $argument) {
            if ($argument instanceof AbstractRequest) {
                $argument->validate($event->getRequest());
            }
        }
    }
}

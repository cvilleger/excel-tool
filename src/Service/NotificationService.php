<?php

namespace App\Service;

use App\Message\NotificationInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class NotificationService
{
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function dispatch(NotificationInterface $message): void
    {
        $this->bus->dispatch($message);
    }
}

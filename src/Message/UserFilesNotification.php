<?php

namespace App\Message;

class UserFilesNotification implements NotificationInterface
{
    private $userEmail;

    public function __construct(string $userEmail)
    {
        $this->userEmail = $userEmail;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }
}

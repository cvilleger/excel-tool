<?php

namespace App\MessageHandler;

use App\Message\UserFilesNotification;

class UserFilesNotificationHandler
{
    public function __invoke(UserFilesNotification $message)
    {
        // ... do some work
    }
}

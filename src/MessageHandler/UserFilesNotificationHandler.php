<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\UserFilesNotification;
use App\Repository\UserRepository;
use App\Service\FileTransformerService;

class UserFilesNotificationHandler
{
    private $fileTransformerService;
    private $userRepository;

    public function __construct(FileTransformerService $fileTransformerService, UserRepository $userRepository)
    {
        $this->fileTransformerService = $fileTransformerService;
        $this->userRepository = $userRepository;
    }

    public function __invoke(UserFilesNotification $message)
    {
        /** @var User $user */
        $user = $this->userRepository->findBy(['email' => $message->getUserEmail()]);
    }
}

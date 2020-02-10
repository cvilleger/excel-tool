<?php

namespace App\Controller;

use App\Message\UserFilesNotification;
use App\Service\FileTransformerService;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @Route("/notification", name="app_notification")
     */
    public function index(FileTransformerService $fileTransformerService)
    {
        $fileTransformerService->convert($this->getUser());

//        $userFilesNotification = new UserFilesNotification($this->getUser()->getUsername());
//        $this->notificationService->dispatch($userFilesNotification);
//
//        $this->addFlash('success', 'Notification sent');

        return $this->redirectToRoute('app_dashboard');
    }
}

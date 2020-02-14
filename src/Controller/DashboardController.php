<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\User;
use App\Form\FilesFormType;
use App\Service\FileTransformerService;
use App\Service\FileUploaderService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private $fileUploaderService;
    private $entityManager;
    private $notificationService;
    private $uploadDir;

    public function __construct(FileUploaderService $fileUploaderService, EntityManagerInterface $entityManager, NotificationService $notificationService, string $uploadDir)
    {
        $this->fileUploaderService = $fileUploaderService;
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
        $this->uploadDir = $uploadDir;
    }

    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(FilesFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedfiles = $form->get('files')->getData();
            /** @var UploadedFile $uploadedfile */
            foreach ($uploadedfiles as $uploadedfile) {
                $filename = $this->fileUploaderService->upload($uploadedfile);
                $file = (new File())
                    ->setName($uploadedfile->getClientOriginalName())
                    ->setSize($uploadedfile->getSize() / 1000)
                    ->setPath($this->uploadDir.'/'.$filename)
                    ->setUser($user)
                ;

                $this->entityManager->persist($file);
            }
            $this->entityManager->flush();
        }

        $totalFileSize = 0;
        foreach ($user->getFiles() as $file) {
            $totalFileSize += $file->getSize();
        }

        return $this->render('dashboard/index.html.twig', [
            'form' => $form->createView(),
            'totalFileSize' => $totalFileSize,
        ]);
    }

    /**
     * @Route("/dashboard/reset", name="app_dashboard_reset")
     */
    public function reset(Filesystem $filesystem)
    {
        /** @var User $user */
        $user = $this->getUser();

        foreach ($user->getFiles() as $file) {
            $filesystem->remove($file->getPath());
            $user->removeFile($file);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'All files successfully removed');

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * @Route("/dashboard/notification", name="app_dashboard_notification")
     */
    public function notification(FileTransformerService $fileTransformerService)
    {
        $filePath = $fileTransformerService->convert($this->getUser());

        return $this->file($filePath);

//        $userFilesNotification = new UserFilesNotification($this->getUser()->getUsername());
//        $this->notificationService->dispatch($userFilesNotification);
//
//        $this->addFlash('success', 'Notification sent');

//        return $this->redirectToRoute('app_dashboard');
    }
}

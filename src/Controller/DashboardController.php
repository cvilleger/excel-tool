<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\User;
use App\Form\FilesFormType;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private $fileUploaderService;
    private $entityManager;

    public function __construct(FileUploaderService $fileUploaderService, EntityManagerInterface $entityManager)
    {
        $this->fileUploaderService = $fileUploaderService;
        $this->entityManager = $entityManager;
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
            $Uploadedfiles = $form->get('files')->getData();
            /** @var UploadedFile $uploadedfile */
            foreach ($Uploadedfiles as $uploadedfile) {
                $filename = $this->fileUploaderService->upload($uploadedfile);
                $file = new File();
                $file->setName($uploadedfile->getClientOriginalName());
                $file->setSize($uploadedfile->getSize());
                $file->setPath($filename);
                $file->setUser($user);

                $this->entityManager->persist($file);
            }
            $this->entityManager->flush();
        }

        return $this->render('dashboard/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

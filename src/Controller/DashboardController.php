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
    private $uploadDir;

    public function __construct(FileUploaderService $fileUploaderService, EntityManagerInterface $entityManager, string $uploadDir)
    {
        $this->fileUploaderService = $fileUploaderService;
        $this->entityManager = $entityManager;
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

        return $this->render('dashboard/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

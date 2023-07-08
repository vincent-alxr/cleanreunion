<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Report;
use App\Form\ReportCreateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class ReportController extends AbstractController
{
    #[Route('/reports', name: 'report_home')]
    public function home(EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $reports = $entityManager->getRepository(Report::class)->findAll();

        foreach ($reports as $report) {
            foreach ($report->getImages() as $image) {
            
                $logger->info($image->getFilename());
            }
        }

        return $this->render('report/report.home.html.twig', [
            "reports" => $reports
        ]);
    }


    #[Route('/reports/create', name: 'report_create', methods:["GET", "POST"])]
    public function create(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $report = new Report();
        $form = $this->createForm(ReportCreateFormType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true, true);
            foreach ($errors as $error) {
                $logger->error($error->getMessage());
            }
        }
        $logger->info('Test');
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les images soumises
            $images = $form->get('images')->getData();

            // Traiter les images
            foreach ($images as $image) {
                $filename = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $filename
                );

                // Créer une instance d'Image et l'associer au signalement
                $imageEntity = new Image();
                $imageEntity->setFilename($filename);
                $report->addImage($imageEntity);
            }

            // Enregistrer le signalement et les images en base de données
            $entityManager->persist($report);
            $entityManager->flush();

            // Rediriger ou afficher un message de succès
            return $this->redirectToRoute('report_home');
        }

        return $this->render('report/report.create.html.twig', [
            'reportForm' => $form->createView(),
        ]);
    }
}
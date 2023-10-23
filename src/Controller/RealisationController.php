<?php

namespace App\Controller;

use App\Entity\Realisation;
use App\Entity\Serie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RealisationController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    #[Route('/sur-mesure', name: 'app_realisation')]
    public function index(): Response
    {
        $series = $this->em->getRepository(Serie::class)->findAll();
        $realisations = $this->em->getRepository(Realisation::class)->findAll();

        return $this->render('realisation/index.html.twig', [
            'realisations' => $realisations,
            'series' => $series
        ]);
    }
}

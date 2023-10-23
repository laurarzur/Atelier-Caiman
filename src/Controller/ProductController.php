<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    #[Route('/boutique', name: 'shop_index')]
    public function index(): Response
    {
        $products = $this->em->getRepository(Produit::class)->findAll();
        $categories = $this->em->getRepository(Categorie::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products, 
            'categories' => $categories
        ]);
    }
}

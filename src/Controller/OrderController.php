<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\RecapDetails;
use App\Form\OrderFormType;
use App\Service\CartService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/commande/creation', name: 'order_index')]
    public function index(CartService $cartService): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(OrderFormType::class, null, [
            'utilisateur' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(), 
            'recapCart' => $cartService->getTotal()
        ]);
    }

    #[Route('/commande/preparation', name: 'order_prepare', methods: ['POST'])]
    public function prepareOrder(CartService $cartService, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(OrderFormType::class, null, [
            'utilisateur' =>$this->getUser() 
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $datetime = new DateTime('now'); 
            $transporteur = $form->get('transporteur')->getData();
            $livraison = $form->get('adresses')->getData();
            $livraisonForCommande = $livraison->getFirstName() . ' ' . $livraison->getLastName();
            if ($livraison->getEntreprise()) {
                $livraisonForCommande .= ' - ' . $livraison->getEntreprise();
            }
            $livraisonForCommande .= '<br>' . $livraison->getTel();
            $livraisonForCommande .= '<br>' . $livraison->getAdresse();
            if ($livraison->getComplement()) {
                $livraisonForCommande .= '<br>' . $livraison->getComplement();
            }
            $livraisonForCommande .= '<br>' . $livraison->getCodePostal() . ' ' . $livraison->getVille();
            $livraisonForCommande .= '<br>' . $livraison->getPays();
            $commande = new Commande(); 
            $reference = $datetime->format('dmY') . '-' . uniqid();
            $commande->setReference($reference);
            $commande->setUtilisateur($this->getUser()); 
            $commande->setCreatedAt($datetime); 
            $commande->setLivraison($livraisonForCommande);
            $commande->setTransporteurNom($transporteur->getNom());
            $commande->setTransporteurPrix($transporteur->getPrix());
            $commande->setIsPaid(0); 
            $paiementMetode = $form->get('paiement')->getData();
            $commande->setPaiement($paiementMetode);

            $this->em->persist($commande);

            foreach ($cartService->getTotal() as $produit) {
                $recapDetails = new RecapDetails();
                $recapDetails->setCommande($commande);
                $recapDetails->setProduit($produit['produit']->getTitre()); 
                $recapDetails->setPrix($produit['produit']->getPrix()); 
                $recapDetails->setQuantity($produit['quantity']); 
                $recapDetails->setTotal($produit['produit']->getPrix() * $produit['quantity']);

                $this->em->persist($recapDetails);
            }
            $this->em->flush();
            return $this->render('order/recap.html.twig', [
                'paiement' => $commande->getPaiement(), 
                'recapCart' => $cartService->getTotal(),
                'transporteur' => $transporteur, 
                'livraison' => $livraisonForCommande, 
                'reference' =>$commande->getReference()
            ]);
        }

        return $this->redirectToRoute('cart_index');
    }
}

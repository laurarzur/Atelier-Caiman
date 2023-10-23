<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route; 
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment; 
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    private EntityManagerInterface $em;
    private UrlGeneratorInterface $generator;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $generator)
    {
        $this->em = $em;
        $this->generator = $generator;
    }


    #[Route('/commande/creer-session-stripe/{reference}', name: 'paiement_stripe', methods: ['POST'])]
    public function stripeCheckout($reference): RedirectResponse
    {
        $productStripe = [];
        $commande = $this->em->getRepository(Commande::class)->findOneBy(['reference' => $reference]);

        if (!$commande) {
            return $this->redirectToRoute('cart_index');
        }

        foreach ($commande->getRecapDetails()->getValues() as $produit) {
            $productData = $this->em->getRepository(Produit::class)->findOneBy(['titre' => $produit->getProduit()]);
            $productStripe[] = [
                'price_data' => [
                    'currency' => 'eur', 
                    'unit_amount' => $productData->getPrix(), 
                    'product_data' => [
                        'name' => $produit->getProduit()
                    ]
                ], 
                'quantity' => $produit->getQuantity()
            ];
        }

        $productStripe[] = [
            'price_data' => [
                'currency' => 'eur', 
                'unit_amount' => $commande->getTransporteurPrix(), 
                'product_data' => [
                    'name' => $commande->getTransporteurNom()
                ]
            ], 
            'quantity' => 1
        ];

        Stripe::setApiKey('sk_test_51O2EiKDBDeRrFGbA6nRw8qxguKXbKtnmLmIVPMW4X7vErGZrEi0bfwmoBKHMKE5edu0vdO8022axPFi7qpnckQMV008IPFZZnx');

        $checkout_session = \Stripe\Checkout\Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                $productStripe
            ]],
            'mode' => 'payment',
            'success_url' => $this->generator->generate('paiement_success', [
                'reference' => $commande->getReference(),

            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generator->generate('paiement_error', [
                'reference' => $commande->getReference(),

            ], UrlGeneratorInterface::ABSOLUTE_URL)
        ]);

        $commande->setStripeId($checkout_session->id);
        $this->em->flush();

        return new RedirectResponse($checkout_session->url);
    }

    #[Route('/commande/succes/{reference}', name: 'paiement_success')]
    public function stripeSuccess($reference, CartService $cartService): Response
    {
        return $this->render('order/success.html.twig');
    } 

    #[Route('/commande/erreur/{reference}', name: 'paiement_error')]
    public function stripeError($reference, CartService $cartService): Response
    {
        return $this->render('order/error.html.twig');
    }

    public function getPaypalClient(): PaypalHttpClient 
    {
        $clientId = "AbmmzgyHhmjUGpDk88BNyt6n00D_-fSY0X5L9m2yBzsGc5Ix4-iOSNyHhpfGHZ9H--_-LLacIrK_FtLH"; 
        $clientSecret = "EBc1Vc4U30DS20hgid9bGCE5HmzlwWf9bASChYpnFagybzZ2tjy25MY2GjgvR8GYFtgL7ABmu1yKe1jF"; 
        $environment = new SandboxEnvironment($clientId, $clientSecret); 
        return new PaypalHttpClient($environment);
    }

    #[Route('/commande/creer-session-paypal', name: 'paiement_paypal', methods: ['POST'])]
    public function paypalCheckout($reference): RedirectResponse
    {
        $commande = $this->em->getRepository(Commande::class)->findOneBy(['reference' => $reference]); 

        if (!$commande) {
            return $this->redirectToRoute('cart_index');
        }

        $items = []; 
        $itemTotal = 0;
        foreach ($commande->getRecapDetails()->getValues() as $produit) {
            $items[] = [
                'name' => $produit->getProduit(), 
                'quantity' => $produit->getQuantity(), 
                'unit_amount' => [
                    'value' => $produit->getPrix(), 
                    'currency_code' => 'EUR'
                ]
            ];
            $itemTotal += $produit->getPrix() * $produit->getQuantity();
        }

        $total = $itemTotal + $commande->getTransporteurPrix();

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');

        $request->body = [
            'intent' => 'CAPTURE', 
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'EUR', 
                        'value' => $total, 
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => 'EUR', 
                                'value' => $itemTotal
                            ], 
                            'shipping' => [
                                'currency_code' => 'EUR', 
                                'value' =>$commande->getTransporteurPrix()
                            ]
                        ]
                    ],
                    'items' => $items
                ]
            ], 
            'application_context' => [
                'return_url' => $this->generator->generate('paiement_success', [
                    'reference' => $commande->getReference()
                    ], UrlGeneratorInterface::ABSOLUTE_URL
                ), 
                'cancel_url' => $this->generator->generate('paiement_error', [
                    'reference' => $commande->getReference()
                    ], UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        ];

        $client = $this->getPaypalClient(); 
        $response = $client->execute($request); 

        if ($response->statusCode != 201) {
            return $this->redirectToRoute('cart_index');
        } 

        $approvalLink = "";        
        foreach ($response->result->links as $link){
            if ($link->rel === "approve") {
                $approvalLink = $link->href; 
                break;
            }
        }

        if (empty($approvalLink)) {
            return $this->redirectToRoute('cart_index');
        }

        $commande->setPaypalId($response->result->id);

        $this->em->flush(); 

        return new RedirectResponse($approvalLink);
    }

        #[Route('/commande/succes/{reference}', name: 'paiement_success')]
        public function paypalSuccess($reference, CartService $cartService): Response
        {
            return $this->render('order/success.html.twig');
        } 

        #[Route('/commande/erreur/{reference}', name: 'paiement_error')]
        public function paypalError($reference, CartService $cartService): Response
        {
            return $this->render('order/error.html.twig');
        }
    
    
}
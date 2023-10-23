<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'cart_index')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getTotal()
        ]);
    }

    #[Route('/mon-panier/add/{id<\d+>}', name: 'cart_add')]
    public function addToCart(CartService $cartService, int $id): Response
    {
        $cartService->addToCart($id);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/mon-panier/decrease/{id<\d+>}', name: 'cart_decrease')]
    public function decreaseInCart(CartService $cartService, int $id): Response
    {
        $cartService->decreaseInCart($id);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/mon-panier/remove/{id<\d+>}', name: 'cart_remove')]
    public function removeFromCart(CartService $cartService, int $id): Response
    {
        $cartService->removeFromCart($id);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/mon-panier/remove-all', name: 'cart_remove-all')]
    public function removeCartAll(CartService $cartService): Response
    {
        $cartService->removeCartAll();
        return $this->redirectToRoute('shop_index');
    }
}
